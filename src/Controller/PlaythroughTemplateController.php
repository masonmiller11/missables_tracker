<?php
	namespace App\Controller;

	use App\DTO\Transformer\ResponseTransformer\PlaythroughResponseDTOTransformer;
	use App\DTO\Transformer\ResponseTransformer\PlaythroughTemplateResponseDTOTransformer;
	use App\Entity\User;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 * Class PlaythroughTemplateController
	 *
	 * @package App\Controller
	 * @Route(path="/templates", name="templates.")
	 */
	class PlaythroughTemplateController extends AbstractController {

		private ValidatorInterface $validator;
		private PlaythroughTemplateResponseDTOTransformer $templateResponseDTOTransformer;

		public function __construct (ValidatorInterface $validator,
									 playthroughTemplateResponseDTOTransformer $playthroughTemplateResponseDTOTransformer) {

			$this->validator = $validator;
			$this->templateResponseDTOTransformer = $playthroughTemplateResponseDTOTransformer;

		}

		/**
		 * @Route(path="/{page<\d+>?1}", methods={"GET"}, name="list")
		 *
		 * @param string|int          $page
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function list(string|int $page, SerializerInterface $serializer): Response {
			//TODO list either my templates or all public templates by author or game.
			//TODO, we can use this to get rid of the private before transforming them!
			// foreach($array as $elementKey => $element) {
			// 	foreach($element as $valueKey => $value) {
			// 		if($valueKey == 'id' && $value == 'searched_value'){
			// 			//delete this particular object from the $array
			// 			unset($array[$elementKey]);
			// 		}
			// 	}
			// }
			//TODO UGHHHHHHHHHHHHHH actually what we should do here is create a query builder. get from where game id
			// equals whatever but visibility is 0. THEN transform and validate it as normal.

			$user = $this->getUser();
			assert($user instanceof User);

			$templates = $user->getPlaythroughTemplates();

			$dto = $this->templateResponseDTOTransformer->transformFromObjects($templates);

			$errors = $this->validator->validate($dto);
			if (count($errors) > 0) {
				$errorString = (string)$errors;
				return new Response($errorString);
			}

			return new Response($serializer->serialize($dto, 'json',[
				'circular_reference_handler' => function ($object) {
					return $object->getId();
				}
			]), Response::HTTP_OK, [
				'Content-Type' => 'application/json'
			]) ;

		}

	}