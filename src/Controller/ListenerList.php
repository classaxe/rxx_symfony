<?php
namespace App\Controller;

use App\Entity\Itu;
use App\Entity\Listeners;
use App\Repository\ItuRepository;
use App\Repository\RegionRepository;
use App\Utils\Rxx;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Required for annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ListenerList
 * @package App\Controller
 */
class ListenerList extends Controller {

    /**
     * @var ItuRepository
     */
    private $country;

    /**
     * @var RegionRepository
     */
    private $region;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Rxx
     */
    private $rxx;

    /**
     * @Route(
     *     "/{system}/listener_list",
     *     requirements={
     *        "system": "reu|rna|rww"
     *     },
     *     name="listener_list"
     * )
     */
    public function listenerListController($system, Request $request, Rxx $rxx, RegionRepository $region, ItuRepository $country)
    {
        $this->rxx = $rxx;
        $this->region = $region;
        $this->request = $request;
        $this->country = $country;

        $types = [];
        foreach ($this->rxx::types as $key => $value){
            $types[$value['label']] = 'type_'.$key;
        }
        $form = $this
            ->createFormBuilder()
            ->add(
                'filter',
                TextType::class,
                array(
                    'label' => 'Search For',
                    'help' => '(Showing all 965 listeners)'
                )
            )
            ->add(
                'types',
                ChoiceType::class,
                array(
                    'expanded' => true,
                    'multiple' => true,
                    'choices' => $types,
                    'choice_attr' => function($choiceValue, $key, $value) {
                        return ['class' => 'type_'.preg_replace("/[^a-z]+/", "", strtolower($key))];
                    },
                )
            )
            ->add(
                'country',
                ChoiceType::class,
                array(
                    'choices' => $country->getAllCountryOptionsForSystem($system),
                )
            )
            ->add('submit', SubmitType::class, array('label' => 'Go'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
//            $task = $form->getData();
//            print "Yo you ".Rxx::y($form->getData());
        }
        $parameters = [
            'system' => $system,
            'mode' => 'Listeners',
            'text' =>
                 "<ul>\n"
                ."    <li>Log and station counts are updated each time new log data is added - "
                ."figures are for logs in the system at this time.</li>\n"
                ."    <li>To see stats for different types of signals, check the boxes shown for 'Types' below.</li>\n"
                ."    <li>This report prints best in Landscape.</li>\n"
                ."</ul>\n",
            'searchResultText' => "(Showing all 965 listeners)",
            'form' => $form->createView()
        ];
        if ($system=='rww') {
            $parameters['regions'] = $this->region->getAllRegions();
        }
        return $this->render('listeners/index.html.twig', $parameters);
    }
}