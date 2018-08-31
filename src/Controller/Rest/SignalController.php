<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-08-23
 * Time: 11:59
 */

namespace App\Controller\Rest;

use App\Entity\Signal;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SignalController
 * @package App\Controller\Rest
 */
class SignalController extends RestBase
{
    /**
     * @return Signal
     */
    private function getEntity()
    {
        return new Signal();
    }

    /**
     * @return string
     */
    private function getEntityType()
    {
        return Signal::class;
    }

    /**
     * Delete Signal
     *
     * @Rest\Delete(
     *     "/signals/{id}",
     *     name="delete_signal"
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
     * Get Signal
     *
     * @Rest\Get(
     *     "/signals/{id}",
     *     name="get_signal"
     * )
     *
     * @return array
     */
    public function getItem($id)
    {
        $entity =       $this->findEntity($this->getEntityType(), $id);
        $httpResponse = ($entity ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        $view =         $this->view($entity, $httpResponse);

        $this->log(__METHOD__, ['id' => $id, 'result' => $entity, 'status' => $httpResponse]);
        return $this->handleView($view);
    }


    /**
     * Get Signal
     *
     * @Rest\Get(
     *     "/signals",
     *     name="get_signals"
     * )
     *
     * @return array
     */
    public function getItems()
    {
        $entities =     $this->findAllEntities($this->getEntityType());
        $httpResponse = ($entities ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        $view =         $this->view($entities, $httpResponse);

        $this->log(__METHOD__, ['result' => $entities, 'status' => $httpResponse]);
        return $this->handleView($view);
    }

    /**
     * Patch Signal
     * @Rest\Patch(
     *     "/signals/{id}",
     *     name="patch_signal"
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
     * Create Signal
     *
     * @Rest\Post(
     *     "/signals",
     *     name="create_signal"
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
            ->setCategory($data['category'])
            ->setSku($data['sku'])
            ->setPrice($data['price'])
            ->setQuantity($data['quantity'])
            ->setCreatedAt($this->getTimestampNow());

        $httpResponse = $this->createEntity($entity);
        $view =         $this->view($entity, $httpResponse);

        $this->log(__METHOD__, ['request' => $data, 'result' => $entity, 'status' => $httpResponse]);
        return $this->handleView($view);
    }

    /**
     * Update Signal
     *
     * @Rest\Put(
     *     "/signals/{id}",
     *     name="update_signal"
     * )
     *
     * @return array
     */
    public function putItem(Request $request, int $id)
    {
        $data = json_decode($request->getContent(), true);

        $entity =       $this->findEntity($this->getEntityType(), $id);
        if ($entity) {
            $entity
                ->setName($data['name'])
                ->setCategory($data['category'])
                ->setSku($data['sku'])
                ->setPrice($data['price'])
                ->setQuantity($data['quantity'])
                ->setModifiedAt($this->getTimestampNow());
        }

        $httpResponse = $this->saveEntity($entity);
        $view =         $this->view($data, $httpResponse);

        $this->log(__METHOD__, ['request' => $data, 'result' => $entity, 'status' => $httpResponse]);
        return $this->handleView($view);
    }
}
