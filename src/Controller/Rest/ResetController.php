<?php
/**
 * Created by PhpStorm.
 * User: mfrancis
 * Date: 2018-08-23
 * Time: 11:59
 */

namespace App\Controller\Rest;

use App\Entity\Category;
use App\Entity\Product;

use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\Response;

class ResetController extends RestBase
{

    /**
     * Reset categories and products from electronic-catalog.json.
     * @Rest\Get(
     *     "/reset",
     *     name="reset"
     * )
     *
     * @return array
     */
    public function resetAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('DELETE from App\Entity\Product');
        $query->execute();

        $query = $em->createQuery('DELETE from App\Entity\Category');
        $query->execute();

        $doc = file_get_contents(
            $this->get('kernel')->getProjectDir().'/public/data/seeds/electronic-catalog.json'
        );
        $json =         json_decode($doc, true);
        $products =     $json['products'];
        $categories =   [];
        foreach ($products as $product) {
            $categories[$product['category']] = $product['category'];
        }
        foreach ($categories as $category) {
            $entity = new Category();
            $entity
                ->setName($category)
                ->setCreatedAt(\DateTime::createFromFormat('U', date('U', time())));
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
        }
        foreach ($products as $product) {
            $entity = new Product();
            $entity
                ->setName($product['name'])
                ->setCategory($product['category'])
                ->setSku($product['sku'])
                ->setPrice($product['price'])
                ->setQuantity($product['quantity'])
                ->setCreatedAt(\DateTime::createFromFormat('U', date('U', time())));
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
        }
        $categories =   $this->getDoctrine()->getRepository(Category::class)->findall();
        $products =     $this->getDoctrine()->getRepository(Product::class)->findall();
        $result = [
            'categories' => $categories,
            'products' =>   $products
        ];
        $httpResponse = Response::HTTP_CREATED;
        $view =         $this->view($result, $httpResponse);

        $this->log(__METHOD__, ['result' => $result, 'status' => $httpResponse]);
        return $this->handleView($view);
    }
}
