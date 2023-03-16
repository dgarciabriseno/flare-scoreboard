<?php declare(strict_types=1);

namespace FlareScoreboard\Models;

class Dataset
{
    /**
     * Human readable identifier for the dataset
     */
    public string $id;

    /**
     * Human readable name for the dataset
     */
    public ?string $title;

    public function __construct(string $id, ?string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public static function fromArray(array $dataset): Dataset
    {
        $title = array_key_exists("title", $dataset) ? $dataset['title'] : NULL;
        return new Dataset($dataset['id'], $title);
    }
}