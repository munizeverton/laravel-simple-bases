<?php


namespace LaravelSimpleBases\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelSimpleBases\Events\UuidModelEvent;
use LaravelSimpleBases\Exceptions\ModelNotFoundException;
use LaravelSimpleBases\Transformers\FileTransformer;

/**
 * Class ModelBase
 * @package App\Models\v2
 * @property Model $findByUuid
 * @property File $files
 */
abstract class ModelBase extends Model
{

    use SoftDeletes;

    protected $dispatchesEvents = [
        'creating' => UuidModelEvent::class
    ];

    /**
     * @param string $uuid
     * @return Model
     */
    public static function findByUuid(string $uuid, bool $withModelNotFound = false)
    {
        $model = self::where('uuid', $uuid)->get()->first();

        if ($withModelNotFound === true and empty($model)) {
            throw new ModelNotFoundException();
        }

        return $model;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class, 'reference_id')
            ->where('reference', get_class($this));
    }

    /**
     * @param null $valueToEmpty
     * @return array|mixed
     */
    public function filesToTransformer($valueToEmpty = null)
    {
        return fractal_transformer(
            $this->files,
            FileTransformer::class,
            $valueToEmpty
        );
    }

}