<?php

namespace App\Repositories\SQL;

use App\Models\Consultation;
use App\Models\EducationalContent;
use App\Repositories\Contracts\FileContract;
use App\Constants\FileConstants;
use App\Repositories\Contracts\EducationalContentContract;
use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator;


class EducationalContentRepository extends BaseRepository implements EducationalContentContract
{
    public function __construct(EducationalContent $model)
    {
        parent::__construct($model);
    }

    public function getAll(array $filters = [], array $relations = []): Collection
    {
        try {
            $query = $this->model->query();

            if (!empty($filters['medical_speciality_ids'])) {
                $query->ofMedicalSpeciality($filters['medical_speciality_ids']);
            }
            
            if (!empty($filters['title_starts_with']) && !empty($filters['locale']) && strlen($filters['title_starts_with']) === 1) {
                $query->ofTitleStartsWith($filters['title_starts_with'], $filters['locale']);
            }

            return $query->with($relations)->get();
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve educational contents: ' . $e->getMessage());
            return new Collection();
        }
    }

    public function search(array $filters = [], array $relations = [], array $data = []): LengthAwarePaginator
    {
        try {
            $query = $this->model->query();

            
            if (!empty($filters['medical_speciality_ids'])) {
                $query->ofMedicalSpeciality($filters['medical_speciality_ids']);
            }

            if (!empty($filters['title_starts_with']) && !empty($filters['locale']) && strlen($filters['title_starts_with']) === 1) {
                $query->ofTitleStartsWith($filters['title_starts_with'], $filters['locale']);
            }
          
            if (!empty($data['order'])) {
                foreach ($data['order'] as $column => $direction) {
                    $query->orderBy($column, $direction);
                }
            }

            $limit = $data['limit'] ?? 10;
            $page = $data['page'] ?? 1;

            return $query->with($relations)->paginate($limit, ['*'], 'page', $page);
        } catch (\Exception $e) {
            // \Log::error('Failed to search educational contents: ' . $e->getMessage());
            return new LengthAwarePaginator([], 0, 10, 1, [
                'path' => request()->url(),
                'query' => request()->query()
            ]);
        }
    }
    
    public function assignToConsultation(Consultation $consultation, array $contentIds, int $doctorId)
    {
        try {
            // if ($consultation->doctor_id !== $doctorId) {
            //     return false;
            // }

            foreach ($contentIds as $contentId) {
                $consultation->educationalContents()->syncWithoutDetaching([
                    $contentId => ['doctor_id' => $doctorId]
                ]);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to assign educational content: ' . $e->getMessage());
            return false;
        }
    }

    public function removeFromConsultation(Consultation $consultation, array $contentIds, int $doctorId): bool
    {
        try {
            // if ($consultation->doctor_id !== $doctorId) {
            //     return false;
            // }
            $consultation->educationalContents()->detach($contentIds);
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to remove educational content: ' . $e->getMessage());
            return false;
        }
    }

    public function getByConsultation(Consultation $consultation, array $relations = []): Collection
    {
        try {
            return $consultation->educationalContents()
                ->where('is_active', true)
                ->with($relations)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve educational content for consultation: ' . $e->getMessage());
            return new Collection();
        }
    }
    
    public function syncRelations($model, $attributes)
    {
        self::syncMainImage($model, $attributes);
        return $model;
    }

    // public static function syncMainImage($model, $attributes)
    // {
    //     if (isset($attributes['main_image'])) {
    //         if ($model->mainImage && $model->mainImage->id != $attributes['main_image'])
    //             resolve(FileContract::class)->remove($model->mainImage);
    //         if (is_file($attributes['main_image'])) {
    //             $file = resolve(FileContract::class)->create([
    //                 'file' => $attributes['main_image'],
    //                 'type' => FileConstants::FILE_TYPE_EDUCATIONAL_MAIN_IMAGE->value
    //             ]);
    //         } else {
    //             $file = resolve(FileContract::class)->find($attributes['main_image']);
    //         }
    //         $model->mainImage()->save($file);
    //     }
    //     return $model;
    // }

    public static function syncMainImage($model, $attributes)
    {
        if (!isset($attributes['main_image'])) {
            return $model;
        }

        $mainImage = $attributes['main_image'];

        // Remove the old image if it exists and is different
        if ($model->mainImage && !($mainImage instanceof \Illuminate\Http\UploadedFile) && $model->mainImage->id != $mainImage) {
            resolve(FileContract::class)->remove($model->mainImage);
        }

        // If the uploaded image is new
        if ($mainImage instanceof \Illuminate\Http\UploadedFile) {
            $file = resolve(FileContract::class)->create([
                'file' => $mainImage,
                'type' => FileConstants::FILE_TYPE_EDUCATIONAL_MAIN_IMAGE->value
            ]);
        } else {
            // If media is an ID of an existing file
            $file = resolve(FileContract::class)->find($mainImage);
        }

        $model->mainImage()->save($file);

        return $model;
    }


}