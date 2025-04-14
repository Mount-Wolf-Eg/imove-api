<?php

namespace App\Constants;

use App\Traits\ConstantsTrait;

enum FileConstants: string
{
    use ConstantsTrait;
    case FILE_TYPE_USER_AVATAR = 'user_avatar';
    case FILE_TYPE_DOCTOR_ATTACHMENTS = 'doctor_attachments';
    case FILE_TYPE_ARTICLE_MAIN_IMAGE = 'article_main_image';

    case FILE_TYPE_ARTICLE_IMAGES = 'article_images';
    case FILE_TYPE_CONSULTATION_ATTACHMENTS = 'consultation_attachments';
    case FILE_TYPE_DOCTOR_UNIVERSITY_CERTIFICATE = 'doctor_university_certificate';
    case FILE_TYPE_VENDOR_ICON = 'vendor_icon';
    case FILE_TYPE_VENDOR_TYPE_ICON = 'vendor_type_icon';
    case MEDICAL_SPECIALTY_ICON = 'medical_specialty_icon';
    case MEDICAL_EQUIPMENT_PHOTO = 'medical_equipment_photo';

    public static function fileableTypes(): array
    {
        return [
            'User',
            'Doctor',
            'Consultation'
        ];
    }
}
