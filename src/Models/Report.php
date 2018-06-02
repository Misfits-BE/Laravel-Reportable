<?php namespace Misfits\Reportable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{HasOne, MorphTo};

class Report extends Model
{
    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var array
     */
    protected $casts = ['meta' => 'array'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reporter(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function conclusion(): HasOne
    {
        return $this->hasOne(Conclusion::class);
    }

    /**
     * Get the judge for the Report Model (only available if there is a conclusion) 
     * 
     * @return mixed
     */
    public function judge()
    {
        return $this->conclusion->judge;
    }

    /**
     * @param  array $data
     * @param  Model $judge
     * @return $this
     */
    public function conclude(array $data, Model $judge)
    {
        $conclusion = (new Conclusion())->fill(array_merge($data, [
            'judge_id' => $judge->id, 'judge_type' => get_class($judge),
        ]));

        $this->conclusion()->save($conclusion);

        return $conclusion;
    }

    /**
     * Get all the users who judged something. 
     * 
     * @return array
     */
    public static function allJudges(): array
    {
        $judges = [];

        foreach (Conclusion::get() as $conclusion) {
            $judges[] = $conclusion->judge;
        }

        return $judges;
    }
}
