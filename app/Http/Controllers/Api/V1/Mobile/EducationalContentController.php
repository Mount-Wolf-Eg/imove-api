<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\AssignEducationalContentRequest;
use App\Http\Requests\EducationalContentFilterRequest;
use App\Http\Resources\EducationalContentResource;
use App\Models\{Consultation, EducationalContent};
use App\Repositories\Contracts\EducationalContentContract;
use App\Repositories\Contracts\LikeContract;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EducationalContentController extends BaseApiController
{
    private LikeContract $likeContract;

    protected array $relations = ['author', 'medicalSpeciality', 'mainImage'];

    public function __construct(EducationalContentContract $contract, LikeContract $likeContract)
    {
        parent::__construct($contract, EducationalContentResource::class);
        $this->likeContract = $likeContract;
    }

    // public function getAll(EducationalContentFilterRequest $request)
    // {
    //     try {
    //         $filters = $request->validated();
    //         $filters['locale'] = $request->header('X-localization', config('app.locale', 'en'));
    //         $contents = $this->contract->getAll($filters, $this->relations);
    //         return $this->respondWithCollection($contents);
    //     } catch (Exception $e) {
    //         return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
    //     }
    // }
    
    public function getAll(EducationalContentFilterRequest $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $order = $request->input('order', []);

            $filters = $request->validated();
            $filters['locale'] = $request->header('Accept-Language', config('app.locale', 'en'));

            $data = array_merge($filters, [
                'order' => $order,
                'limit' => $limit,
                'page' => $page,
            ]); 

            $contents = $this->contract->search($filters, $this->relations, $data);

            return $this->respondWithCollection($contents);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function show($id)
    {
        try {
            $educationalContent = $this->contract->findOrFail($id, $this->relations);
            return $this->respondWithModel($educationalContent);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    public function assignToConsultation(Consultation $consultation, AssignEducationalContentRequest $request)
    {
        try {
            $doctor = auth()->user()->doctor;
            // return $doctor->id;
            $success = $this->contract->assignToConsultation($consultation, $request->validated()['content_ids'], $doctor->id);

            if (!$success) {
                return $this->respondWithError( __('messages.Unauthorized: This consultation is not for you'), Response::HTTP_FORBIDDEN);
            }

            return $this->respondWithArray(['message' => __('messages.create_success')], [], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function removeFromConsultation(Consultation $consultation, AssignEducationalContentRequest $request)
    {
        try {
            $doctor = auth()->user()->doctor;

            $success = $this->contract->removeFromConsultation($consultation, $request->validated()['content_ids'], $doctor->id);
            if (!$success) {
                return $this->respondWithError( __('messages.Unauthorized: This consultation is not for you'), Response::HTTP_FORBIDDEN);
            }

            return $this->respondWithArray(['message' => __('messages.delete_success')], [], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getByConsultation(Consultation $consultation)
    {
        try {
            $contents = $this->contract->getByConsultation($consultation, $this->relations);
            return $this->respondWithCollection($contents);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    
    public function toggleLike(EducationalContent $content): JsonResponse
    {
        try {
            if (!$content->exists) {
                return $this->respondWithError( __('messages.Educational content not found'), Response::HTTP_NOT_FOUND);
            }

            $liked = $this->likeContract->toggleRecord($content);
            // return $this->respondWithModel($content);
            return $this->respondWithArray([
                'message' => $liked ?  __('messages.Like added successfully') : __('messages.Like removed successfully'),
                'data' => ['auth_like_status' => $liked]
            ]);
        } catch (Exception $e) {
            \Log::error('Failed to toggle like: ' . $e->getMessage());
            return $this->respondWithError('An error occurred while processing the like', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}