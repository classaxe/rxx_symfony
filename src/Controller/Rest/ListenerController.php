<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-08-23
 * Time: 11:59
 */

namespace App\Controller\Rest;

use App\Entity\Listener;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ListenerController
 * @package App\Controller\Rest
 */
class ListenerController extends RestBase
{
    /**
     * @return Listener
     */
    private function getEntity()
    {
        return new Listener();
    }

    /**
     * @return string
     */
    private function getEntityType()
    {
        return Listener::class;
    }

    /**
     * Delete Listener
     * @Rest\Delete(
     *     "/listeners/{id}",
     *     name="delete_listener"
     * )
     */
    public function deleteItem($id)
    {
        $entity =       $this->findEntity($this->getEntityType(), $id);
        $httpResponse = $this->deleteEntity($entity);
        $view =         $this->view($entity, $httpResponse);

        $this->log(__METHOD__, ['id' => $id, 'status' => $httpResponse]);
        return $this->handleView($view);
    }

    /**
     * Get Listener
     * @Rest\Get(
     *     "/listeners/{id}",
     *     name="get_listener"
     * )
     *
     * @return array
     */
    public function getItem($id)
    {
        $entity =       $this->findEntity($this->getEntityType(), $id);
        $httpResponse = ($entity ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        $view =         $this->view($entity, $httpResponse);

        $this->log(__METHOD__, ['id' => $id, 'status' => $httpResponse]);
        return $this->handleView($view);
    }

    /**
     * Get Listeners
     * @Rest\Get(
     *     "/listeners",
     *     name="get_listeners"
     * )
     *
     * @return array
     */
    public function getItems()
    {
        $this->memoryLogger('start');
        $entities =     $this->findAllEntities($this->getEntityType());
        $httpResponse = ($entities ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        $view =         $this->view($entities, $httpResponse);
        $result =       $this->handleView($view);
        $memory =       number_format($this->memoryLogger('stop'));
        $this->log(__METHOD__, ['status' => $httpResponse, 'memory' => $memory]);
        return $result;
    }

    /**
     * Patch Listener
     * @Rest\Patch(
     *     "/listeners/{id}",
     *     name="patch_listener"
     * )
     *
     * @return array
     */
    public function patchItem(Request $request, int $id)
    {
        $data = json_decode($request->getContent(), true);

        $entity =       $this->findEntity($this->getEntityType(), $id);
        if ($entity) {
            if ($data) {
                foreach ($data as $key => $value) {
                    if (method_exists($entity, 'set'.ucwords($key))) {
                        call_user_func_array([ $entity, 'set'.ucwords($key)], [$value]);
                    }
                }
            }
            $entity->setModifiedAt($this->getTimestampNow());
        }

        $httpResponse = $this->saveEntity($entity);
        $view =         $this->view($entity, $httpResponse);

        $this->log(__METHOD__, ['request' => $data, 'result' => $entity, 'status' => $httpResponse]);
        return $this->handleView($view);
    }

    /**
     * Create Listener
     * @Rest\Post(
     *     "/listeners",
     *     name="create_listener"
     * )
     *
     * @return array
     */
    public function postItem(Request $request)
    {
        $data =         json_decode($request->getContent(), true);

        $entity =       $this->getEntity();
        $entity
            ->setName($data['name'])
            ->setCreatedAt($this->getTimestampNow());

        $httpResponse = $this->createEntity($entity);
        $view =         $this->view($entity, $httpResponse);

        $this->log(__METHOD__, ['request' => $data, 'result' => $entity, 'status' => $httpResponse]);
        return $this->handleView($view);
    }

    /**
     * Update Listener
     * @Rest\Put(
     *     "/listeners/{id}",
     *     name="update_listener"
     * )
     */
    public function putItem(Request $request, int $id)
    {
        $data = json_decode($request->getContent(), true);

        $entity =       $this->findEntity($this->getEntityType(), $id);
        if ($entity) {
            $entity
                ->setName($data['name'])
                ->setModifiedAt($this->getTimestampNow());
        }

        $httpResponse = $this->saveEntity($entity);
        $view =         $this->view($entity, $httpResponse);

        $this->log(__METHOD__, ['request' => $data, 'result' => $entity, 'status' => $httpResponse]);
        return $this->handleView($view);
    }
}
