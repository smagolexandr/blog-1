<?php

namespace AppBundle\Repository\Blog;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function getBlogPosts()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT p, c, u
                             FROM AppBundle:Blog\Post p
                             JOIN p.user u
                             LEFT JOIN p.comments c
                             ORDER BY p.createdAt DESC
                            ')
            ->getResult();
    }

    public function getUnapprovedPosts()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT p, c, u
                             FROM AppBundle:Blog\Post p
                             JOIN p.user u
                             LEFT JOIN p.comments c
                             WHERE p.approved = 0
                             ORDER BY p.createdAt DESC
                            ')
            ->getResult();
    }

    public function getBlogPostsByParams($params)
    {
        $em = $this->getEntityManager();

        $postsQuery = $em->createQueryBuilder()
            ->select('p', 't')
            ->from('AppBundle:Blog\Post', 'p')
            ->leftJoin('p.tags', 't')
            ->where('p.approved = 1')
            ->orderBy('p.createdAt' ,'DESC');

        if ($params->has('tag') && $params->get('tag')){
            $postsQuery->where('t.name = ?1 AND p.approved = 1')
                ->setParameter(1, $params->get('tag'));
        }

        if ($params->has('title') && $params->get('title')) {
            $postsQuery->where('p.title LIKE :title AND p.approved = 1')
                ->setParameter('title', '%' . $params->get('title') . '%');
        }
        return $postsQuery->getQuery()->getResult();
    }

    public function getPopularPosts(){
        return $this->getEntityManager()
            ->createQuery('SELECT p
                            FROM AppBundle:Blog\Post p
                            WHERE p.approved = 1
                            ORDER BY p.views DESC')
            ->setMaxResults(5)
            ->getResult();
    }
}
