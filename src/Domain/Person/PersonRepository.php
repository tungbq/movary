<?php declare(strict_types=1);

namespace Movary\Domain\Person;

use Doctrine\DBAL\Connection;
use Movary\ValueObject\Date;
use Movary\ValueObject\DateTime;
use Movary\ValueObject\Gender;
use RuntimeException;

class PersonRepository
{
    public function __construct(private readonly Connection $dbConnection)
    {
    }

    public function create(
        int $tmdbId,
        string $name,
        Gender $gender,
        ?string $knownForDepartment,
        ?string $tmdbPosterPath,
        ?Date $birthDate = null,
        ?Date $deathDate = null,
        ?string $placeOfBirth = null,
        ?DateTime $updatedAtTmdb = null,
    ) : PersonEntity {
        $this->dbConnection->insert(
            'person',
            [
                'name' => $name,
                'gender' => $gender->asInt(),
                'known_for_department' => $knownForDepartment,
                'tmdb_id' => $tmdbId,
                'tmdb_poster_path' => $tmdbPosterPath,
                'birth_date' => $birthDate === null ? null : (string)$birthDate,
                'death_date' => $deathDate === null ? null : (string)$deathDate,
                'place_of_birth' => $placeOfBirth,
                'updated_at_tmdb' => $updatedAtTmdb === null ? null : (string)$updatedAtTmdb,
                'created_at' => (string)DateTime::create(),
            ],
        );

        return $this->fetchById((int)$this->dbConnection->lastInsertId());
    }

    public function deleteAllNotReferenced() : void
    {
        $this->dbConnection->executeQuery(
            'DELETE
            FROM person
            WHERE id NOT IN (
                SELECT person_id FROM movie_cast
                UNION
                SELECT person_id FROM movie_crew
            )',
        );
    }

    public function fetchAllOrderedByLastUpdatedAtTmdbAsc(?int $limit = null) : \Traversable
    {
        $query = 'SELECT * FROM `person` ORDER BY updated_at_tmdb ASC';

        if ($limit !== null) {
            $query .= ' LIMIT ' . $limit;
        }

        return $this->dbConnection->prepare($query)->executeQuery()->iterateAssociative();
    }

    public function findByPersonId(int $personId) : ?PersonEntity
    {
        $data = $this->dbConnection->fetchAssociative('SELECT * FROM `person` WHERE id = ?', [$personId]);

        if ($data === false) {
            return null;
        }

        return PersonEntity::createFromArray($data);
    }

    public function findByTmdbId(int $tmdbId) : ?PersonEntity
    {
        $data = $this->dbConnection->fetchAssociative('SELECT * FROM `person` WHERE tmdb_id = ?', [$tmdbId]);

        if ($data === false) {
            return null;
        }

        return PersonEntity::createFromArray($data);
    }

    public function update(
        int $id,
        int $tmdbId,
        string $name,
        Gender $gender,
        ?string $knownForDepartment,
        ?string $tmdbPosterPath,
        ?Date $birthDate = null,
        ?Date $deathDate = null,
        ?string $placeOfBirth = null,
        ?DateTime $updatedAtTmdb = null,
    ) : PersonEntity {
        $payload = [
            'name' => $name,
            'gender' => $gender->asInt(),
            'known_for_department' => $knownForDepartment,
            'tmdb_id' => $tmdbId,
            'tmdb_poster_path' => $tmdbPosterPath,
            'birth_date' => $birthDate === null ? null : (string)$birthDate,
            'death_date' => $deathDate === null ? null : (string)$deathDate,
            'place_of_birth' => $placeOfBirth,
            'updated_at' => (string)DateTime::create(),
        ];

        if ($updatedAtTmdb !== null) {
            $payload['updated_at_tmdb'] = (string)$updatedAtTmdb;
        }

        $this->dbConnection->update(
            'person', $payload, ['id' => $id],
        );

        return $this->fetchById($id);
    }

    private function fetchById(int $id) : PersonEntity
    {
        $data = $this->dbConnection->fetchAssociative('SELECT * FROM `person` WHERE id = ?', [$id]);

        if ($data === false) {
            throw new RuntimeException('No genre found by id: ' . $id);
        }

        return PersonEntity::createFromArray($data);
    }
}
