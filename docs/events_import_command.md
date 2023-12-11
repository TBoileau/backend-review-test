# Import Github Events Command

## Usage

First, make sure you open a terminal in the right container  :
```bash
$ make shell
```

You can now execute the command :
```bash
$ php bin/console app:import-github-events 2023-01-01
```

Date must be in ISO 8601 format (YYYY-MM-DD).

Note: if you do not specify a date, the current date will be used by default.

## Architecture Decision Records

### Requirements
* Consume the least amount of memory
* Process gzipped files

### Potential solutions
* Compile zlib with curl to automatically decode the HTTP response
* Take advantage of the fact that the file is in NDJSON format to be able to decode it (gzip + json) more easily, line by line.
* Process events batch rather than individual ones
* Extend repositories to manage batch processing
* Create an entity to store temporary events and process data in a Trigger/Stored Procedure combo
* Simple batch processing with Entity Manager
* Using Messenger to process asynchronous events batches
* Set up Redis to improve Message Broker performance
* Use the concurrency to download a day's worth of files

### Selected solution
* Take advantage of the fact that the file is in NDJSON format to be able to decode it (gzip + json) more easily, line by line.
* Process events batch rather than individual ones
* Use the concurrency to download a day's worth of files
* Using Messenger to process asynchronous events batches
* Set up Redis to improve Message Broker performance
* Simple batch processing with Entity Manager* Create an entity to store temporary events and process data in a Trigger/Stored Procedure combo

### What are the reasons for this ?
After several attempts, it has become clear that using Messenger asynchronously remains one of the only viable solutions. In fact, on any given day, you'd have to download 24 gzipped files of around 100MB, containing millions of Github events. It's unthinkable to believe that all this can be managed synchronously without a memory problem.

As for using Redis, the difference between using Doctrine and Redis is so obvious.

For the implementation of a temporary entity, the aim was to manage the data in a flat way, so as to avoid reading bottlenecks during batch processing.

All that remained was to process the data with a Trigger and a Stored Procedure, leaving it to the database to manage the data in the `event`, `actor` and `repo` tables.

### ETL or Extract Transform Load
This is the most appropriate choice. We can see the 3 stages of a simple ETL architecture:
1. Extract files from gharchive.org
2. Transform the gzipped NDJSON into normamlized data (array)
3. Load data into the persistence layer
