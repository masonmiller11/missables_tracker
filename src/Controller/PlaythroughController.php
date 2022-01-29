<?php
	namespace App\Controller;

	use App\Entity\Playthrough\Playthrough;
	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;
	use App\Payload\Registry\PayloadDecoderRegistryInterface;
	use App\Repository\PlaythroughRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Request\Payloads\PlaythroughPayload;
	use App\Request\Payloads\SectionPayload;
	use App\Request\Payloads\StepPayload;
	use App\Service\ResponseHelper;
	use App\Transformer\PlaythroughEntityTransformer;
	use App\Transformer\SectionEntityTransformer;
	use App\Transformer\StepEntityTransformer;
	use http\Exception\InvalidArgumentException;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * @Route(path="/playthroughs/", name="playthroughs.")
	 */
	final class PlaythroughController extends AbstractBaseApiController implements BaseApiControllerInterface {

		private PlaythroughTemplateRepository $playthroughTemplateRepository;

		private SectionEntityTransformer $sectionEntityTransformer;

		private StepEntityTransformer $stepEntityTransformer;

		private SerializerInterface $serializer;

		/**
		 * PlaythroughController constructor.
		 *
		 * @param PlaythroughEntityTransformer $entityTransformer
		 * @param PlaythroughRepository $repository
		 * @param PayloadDecoderRegistryInterface $decoderRegistry
		 * @param PlaythroughTemplateRepository $playthroughTemplateRepository
		 * @param SectionEntityTransformer $sectionEntityTransformer
		 * @param StepEntityTransformer $stepEntityTransformer
		 * @param SerializerInterface $serializer
		 */
		public function __construct(
			PlaythroughEntityTransformer $entityTransformer,
			PlaythroughRepository $repository,
			PayloadDecoderRegistryInterface $decoderRegistry,
			PlaythroughTemplateRepository $playthroughTemplateRepository,
			SectionEntityTransformer $sectionEntityTransformer,
			StepEntityTransformer $stepEntityTransformer,
			SerializerInterface $serializer
		) {

			parent::__construct(
				$entityTransformer,
				$repository,
				$decoderRegistry->getDecoder(PlaythroughPayload::class)
			);

			$this->playthroughTemplateRepository = $playthroughTemplateRepository;
			$this->sectionEntityTransformer = $sectionEntityTransformer;
			$this->stepEntityTransformer = $stepEntityTransformer;
			$this->serializer = $serializer;

		}

		/**
		 * @Route(path="create", methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function createNew(Request $request): Response {

			try {
				$playthrough = $this->doCreate($request, $this->getUser());

				if (!($playthrough instanceof Playthrough))
					throw new InvalidArgumentException();

				$playthroughTemplate = $this->playthroughTemplateRepository->find($playthrough->getTemplateId());
				$sectionTemplates = $playthroughTemplate->getSections();

				foreach ($sectionTemplates as $sectionTemplate) {
					$sectionPayload = new SectionPayload();
					$sectionPayload->description = $sectionTemplate->getDescription();
					$sectionPayload->playthroughId = $playthrough->getId();
					$sectionPayload->name = $sectionTemplate->getName();
					$sectionPayload->position = $sectionTemplate->getPosition();

					$section = $this->sectionEntityTransformer->create($sectionPayload);

					$stepTemplates = $sectionTemplate->getSteps();

					foreach ($stepTemplates as $stepTemplate) {
						$stepPayload = new StepPayload;
						$stepPayload->description = $stepTemplate->getDescription();
						$stepPayload->name = $stepTemplate->getName();
						$stepPayload->position = $stepTemplate->getPosition();
						$stepPayload->sectionId = $section->getId();

						$this->stepEntityTransformer->create($stepPayload);

					}

				}

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

			return ResponseHelper::createResourceCreatedResponse(
				'playthroughs/read/' . $playthrough->getId(),
				$playthrough->getId()
			);

		}

		/**
		 * @Route(path="delete/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param string|int $id
		 *
		 * @return Response
		 */
		public function delete(string|int $id): Response {

			$this->doDelete($id);

			return ResponseHelper::createResourceDeletedResponse();

		}

		/**
		 * @Route(path="{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"}, name="list")
		 *
		 * @param int $page
		 * @param int $pageSize
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function list(int $page, int $pageSize, SerializerInterface $serializer): Response {

			$ownerId = $this->getUser()->getId();

			if (!$this->repository instanceof PlaythroughRepository)
				throw new \InvalidArgumentException(
					'repository not instance of type PlaythroughRepository'
				);

			$playthroughs = $this->repository->findAllByOwner($ownerId, $page, $pageSize);

			return ResponseHelper::createReadResponse($playthroughs, $serializer);

		}

		/**
		 * @Route(path="read/{id<\d+>}",methods={"GET"}, name="read")
		 *
		 * @param int $id
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function read(int $id, SerializerInterface $serializer): Response {

			$playthrough = $this->repository->find($id);

			return ResponseHelper::createReadResponse($playthrough, $serializer);

		}

		/**
		 * @Route(path="update/{id<\d+>}", methods={"PATCH"}, name="update")
		 *
		 * @param Request $request
		 * @param string|int $id
		 *
		 * @return Response
		 */
		public function update(Request $request, string|int $id): Response {

			try {

				$playthrough = $this->doUpdate($request, $id);

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

			return ResponseHelper::createResourceUpdatedResponse('playthroughs/read/' . $playthrough->getId());

		}

		public function create(Request $request): Response {
			// TODO: Implement create() method.
		}
	}