<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\Gateway;

use Doctrine\DBAL\Connection;

final class DoctrineDatabase
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns number of content objects that have any collection.
     */
    public function getContentsWithCollectionsCount(): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select(
            'COUNT(DISTINCT eic.contentobject_id) AS count',
        )
            ->from('ezinfocollection', 'eic')
            ->innerJoin(
                'eic',
                'ibexa_content',
                'ic',
                $query->expr()->eq(
                    'eic.contentobject_id',
                    'ic.id',
                ),
            )
            ->leftJoin(
                'eic',
                'ibexa_content_tree',
                'ict',
                $query->expr()->eq(
                    'eic.contentobject_id',
                    'ict.contentobject_id',
                ),
            );

        $data = $query->fetchAllAssociative();

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Returns content objects with their collections.
     */
    public function getObjectsWithCollections(int $limit, int $offset): array
    {
        $contentIdsQuery = $this->connection->createQueryBuilder();
        $contentIdsQuery
            ->select('DISTINCT contentobject_id AS id')
            ->from('ezinfocollection');

        $contents = [];
        foreach ($contentIdsQuery->fetchAllAssociative() as $content) {
            $contents[] = (int) $content['id'];
        }

        if (empty($contents)) {
            return [];
        }

        $query = $this->connection->createQueryBuilder();
        $query
            ->select('ic.id AS content_id', 'ict.main_node_id')
            ->from('ibexa_content', 'ic')
            ->leftJoin(
                'ic',
                'ibexa_content_tree',
                'ict',
                $query->expr()->eq('ic.id', 'ict.contentobject_id'),
            )
            ->innerJoin(
                'ic',
                'ibexa_content_type',
                'ictype',
                $query->expr()->eq('ic.content_type_id', 'ictype.id'),
            )
            ->andWhere($query->expr()->in('ic.id', $contents))
            ->groupBy(['ict.main_node_id', 'content_id'])
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $query->fetchAllAssociative();
    }
}
