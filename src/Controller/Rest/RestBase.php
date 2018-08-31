<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-08-24
 * Time: 05:26
 */

namespace App\Controller\Rest;

use Psr\Log\LoggerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;

class RestBase extends FOSRestController
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    protected function log($source, $data = [])
    {
        $this->logger->info($source, $data);
    }

    protected function deleteEntity($entity)
    {
        if (!$entity) {
            return Response::HTTP_NOT_FOUND;
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($entity);
        $em->flush();
        return Response::HTTP_NO_CONTENT;
    }

    protected function findAllEntities($entityType)
    {
        $repository = $this->getDoctrine()->getRepository($entityType);
        return $repository->findAll();
    }


    protected function findEntity($entityType, $id)
    {
        $repository = $this->getDoctrine()->getRepository($entityType);
        return $repository->find($id);
    }

    protected function getTimestampNow()
    {
        return \DateTime::createFromFormat('U', date('U', time()));
    }

    protected function createEntity($entity)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        return Response::HTTP_CREATED;
    }

    protected function saveEntity($entity)
    {
        if (!$entity) {
            return Response::HTTP_NOT_FOUND;
        }
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return Response::HTTP_OK;
    }
}
