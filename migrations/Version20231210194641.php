<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231210194641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE temp_events_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE temp_events (id INT NOT NULL, event_id BIGINT NOT NULL, type VARCHAR(255) CHECK(type IN (\'COM\', \'MSG\', \'PR\')) NOT NULL, payload JSONB NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, count INT NOT NULL, actor_id BIGINT NOT NULL, actor_login VARCHAR(255) NOT NULL, actor_url VARCHAR(255) NOT NULL, actor_avatar_url VARCHAR(255) NOT NULL, repo_id BIGINT NOT NULL, repo_name VARCHAR(255) NOT NULL, repo_url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN temp_events.type IS \'(DC2Type:EventType)\'');
        $this->addSql('COMMENT ON COLUMN temp_events.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(<<<SQL
CREATE FUNCTION handle_event()
    RETURNS TRIGGER
    LANGUAGE plpgsql
AS $$
BEGIN
    
    INSERT INTO actor (id, login, url, avatar_url)
    VALUES (NEW.actor_id, NEW.actor_login, NEW.actor_url, NEW.actor_avatar_url)
    ON CONFLICT (id) DO UPDATE SET
        login = EXCLUDED.login,
        url = EXCLUDED.url,
        avatar_url = EXCLUDED.avatar_url;

    INSERT INTO repo (id, name, url)
    VALUES (NEW.repo_id, NEW.repo_name, NEW.repo_url)
    ON CONFLICT (id) DO UPDATE SET
        name = EXCLUDED.name,
        url = EXCLUDED.url;

    INSERT INTO "event" (id, actor_id, repo_id, type, count, payload, create_at, comment)
    VALUES (NEW.event_id, NEW.actor_id, NEW.repo_id, NEW.type, NEW.count, NEW.payload, NEW.created_at, null)
    ON CONFLICT (id) DO UPDATE SET
        actor_id = EXCLUDED.actor_id,
        repo_id = EXCLUDED.repo_id,
        type = EXCLUDED.type,
        count = EXCLUDED.count,
        payload = EXCLUDED.payload,
        create_at = EXCLUDED.create_at;

    RETURN NEW;
END;
$$
SQL
        );
        $this->addSql(<<<SQL
CREATE TRIGGER after_insert_event
    AFTER INSERT ON temp_events
    FOR EACH ROW
    EXECUTE PROCEDURE handle_event();
SQL
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE temp_events_id_seq CASCADE');
        $this->addSql('DROP TABLE temp_events');
        $this->addSql('DROP TRIGGER after_insert_event ON temp_events');
        $this->addSql('DROP FUNCTION handle_event()');
    }
}
