<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;

class StoreProjectRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = array_merge($this->getMainRules(), $this->getSeriesRules());
        return $rules;
    }

    /**
     * @return array
     */
    public function getEpisodes()
    {
        $episodes = [];
        if (is_array($this->get('season'))) {
            foreach ($this->get('season') as $i => $val) {
                $episodes[] = [
                    'season' => $val,
                    'episode' => $this->get('episode')[$i],
                    'originalTitle' => $this->get('episode_original_title')[$i],
                    'translatedTitle' => $this->get('episode_translated_title')[$i],
                    'year' => $this->get('episode_year')[$i],
                    'imdbId' => $this->get('episode_imdb_id')[$i],
                    'id' => $this->get('episode_id')[$i]
                ];
            }
        }
        return $episodes;
    }

    /**
     * @return array
     */
    protected function getMainRules()
    {
        $beforeYear = date('Y',strtotime('+1 year'));
        $rules = [
            'translated_title' => 'required|max:100',
            'original_title' => 'required|max:100',
            'imdb' => 'max:20',
            'score' => array('regex:/^((10){1}|(\\d){1}[\\.,]{1}(\\d){1})?$/'),
            'plot' => 'max:500',
            'poster' => 'required|max:1000000',
            'lang' => 'required|size:3',
            'movie-type' => 'required',
            'year' => 'required_if:movie-type,movie|date_format:Y|before:' . $beforeYear . '|after:1899'
        ];
        return $rules;
    }

    /**
     * @return array
     */
    protected function getSeriesRules()
    {
        $rules = [];
        $beforeYear = date('Y',strtotime('+1 year'));
        if (is_array($this->get('season'))) {
            foreach ($this->get('season') as $key => $val) {
                $rules['season.' . $key] = 'required|max:99|numeric';
            }
            foreach ($this->get('episode') as $key => $val) {
                $rules['episode.' . $key] = 'required|max:99|numeric';
            }
            foreach ($this->get('episode_original_title') as $key => $val) {
                $rules['episode_original_title.' . $key] = 'required|max:100';
            }
            foreach ($this->get('episode_translated_title') as $key => $val) {
                $rules['episode_translated_title.' . $key] = 'required|max:100';
            }
            foreach ($this->get('episode_year') as $key => $val) {
                $rules['episode_year.' . $key] = 'required|date_format:Y|before:' . $beforeYear . '|after:1899';
            }
            foreach ($this->get('episode_imdb_id') as $key => $val) {
                $rules['episode_imdb_id.' . $key] = 'max:20';
            }
        }
        return $rules;
    }
}
