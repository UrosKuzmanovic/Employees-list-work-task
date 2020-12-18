<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Title;
use App\Form\EmployeeType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MainController extends AbstractController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    public $employees;

    public function __construct(Environment $twig, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="employee_list")
     */
    public function index()
    {
        $this->employees = $this->getDoctrine()->getRepository(Employee::class)->findAll();
        return $this->render('employees/employeeList.html.twig', ['employees' => $this->employees]);
    }

    /**
     * @Route("/add", name="add_employee")
     * @param Request            $request
     * @param ValidatorInterface $validator
     */
    public function addEmployee(Request $request, ValidatorInterface $validator)
    {
        $employee = new Employee();
        $form = $this->formFactory->create(EmployeeType::class, $employee);
        $form->get('birthday')->setData((new \DateTime())->format('d/m/y'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $titles = $form["titles"]->getData();
            $employee->setTitles($titles);

            $errors = $validator->validate($employee);
            if(count($errors) > 0){
                $errorsString = (string) $errors;
                return new Response($errorsString);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirect('/');
        }

        return new Response($this->twig->render('employees/addEmployee.html.twig', ['form' => $form->createView()]));
    }

    /**
     * @Route("/edit", name="edit_employee")
     * @param Request            $request
     * @param ValidatorInterface $validator
     */
    public function editEmployee(Request $request, ValidatorInterface $validator)
    {
        $id = $request->get('id');
        $employee = $this->getDoctrine()->getRepository(Employee::class)->findOneBy(['id' => $id]);
        $form = $this->formFactory->createBuilder(EmployeeType::class, $employee)->getForm();
        $form->get('birthday')->setData($employee->getBirthday()->format('d/m/y'));
        $form->get('titles')->setData($employee->getTitles());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $titles = $form["titles"]->getData();
            $employee->setTitles($titles);

            $errors = $validator->validate($employee);
            if(count($errors) > 0){
                $errorsString = (string) $errors;
                return new Response($errorsString);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirect('/');
        }

        return new Response($this->twig->render('employees/addEmployee.html.twig', ['form' => $form->createView()]));
    }

    /**
     * @Route("/search-titles", methods={"POST"}, name="search_titles")
     * @param Request $request
     */
    public function searchTitles(Request $request)
    {
        $searchTerm = '%'.$request->get('searchTerm').'%';
        $titles = $this->getDoctrine()->getRepository(Title::class)->findByName($searchTerm);

        $normalizers = [
            new ObjectNormalizer()
        ];
        $encoders = [
            new JsonEncoder()
        ];
        $serializer = new Serializer($normalizers, $encoders);
        $serializedTitles = $serializer->serialize($titles, 'json', ['ignored_attributes' => ['employees']]);

        return new Response($serializedTitles);
    }

    /**
     * @Route("/activity-employees", methods={"POST"}, name="activity_employees")
     * @param Request $request
     */
    public function changeActivity(Request $request){
        $activity = $request->get('activity');
        if ($activity == 0 || $activity == 1){
            $this->employees = $this->getDoctrine()->getRepository(Employee::class)->findByActivity($activity);
        } else {
            $this->employees = $this->getDoctrine()->getRepository(Employee::class)->findAll();
        }
        return $this->render('employees/employeeTable.html.twig', ['employees' => $this->employees]);
    }

    /*
     * @Route("/activity-employees", methods={"POST"}, name="activity_employees")
     * @param Request $request
     *
    public function changeActivity(Request $request){
        $activity = $request->get('activity');
        if ($activity == 0 || $activity == 1){
            $this->employees = $this->getDoctrine()->getRepository(Employee::class)->findByActivity($activity);
        } else {
            $this->employees = $this->getDoctrine()->getRepository(Employee::class)->findAll();
        }
        $normalizers = [
            new ObjectNormalizer()
        ];
        $encoders = [
            new JsonEncoder()
        ];
        $serializer = new Serializer($normalizers, $encoders);
        $serializedEmployees = $serializer->serialize($this->employees, 'json', ['ignored_attributes' => ['titles']]);

        return new Response($serializedEmployees);
    }*/

    /**
     * @Route("/employee-table", methods={"POST"}, name="employee_table")
     * @param Request $request
     */
    public function employeeTable(Request $request){
        $activity = $request->get('activityy');
        if ($activity == 0 || $activity == 1){
            $this->employees = $this->getDoctrine()->getRepository(Employee::class)->findByActivity($activity);
        } else {
            $this->employees = $this->getDoctrine()->getRepository(Employee::class)->findAll();
        }
        return $this->render('employees/employeeTable.html.twig', ['employees' => $this->employees]);
    }

}
