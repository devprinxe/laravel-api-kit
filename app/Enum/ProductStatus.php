<?php

namespace App\Enum;

enum ProductStatus: string
{
    case DRAFT = 'DRAFT';
    case IN_REVIEW = 'IN_REVIEW';
    case PENDING = 'PENDING';
    case REJECTED = 'REJECTED';
    case PUBLISHED = 'PUBLISHED';
    case ARCHIVED = 'ARCHIVED';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::IN_REVIEW => 'In Review',
            self::PENDING => 'Pending',
            self::REJECTED => 'Rejected',
            self::PUBLISHED => 'Published',
            self::ARCHIVED => 'Archived',
        };
    }
}
