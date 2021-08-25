<?php

namespace Modules\Projects\Services;

use Modules\Projects\Services\LatinConverter\Renderers\DefaultRenderer;

class LatinConverter
{
    const CLASSIC_ORTHOGRAPHY = 't';
    const ACADEMIC_ORTHOGRAPHY = 'n';

    /**
     * @var string
     */
    protected $orthography;

    /**
     * @var array
     */
    protected $rendersMaps = [
        self::ACADEMIC_ORTHOGRAPHY => [
            'ApostropheRenderer',
            'SoftSignRenderer',
            'ZzRenderer',
            'ZRenderer',
            'YRenderer',
            'VRenderer',
            'UNonSyllabicRenderer',
            'TRenderer',
            'SsRenderer',
            'SRenderer',
            'RRenderer',
            'RRenderer',
            'PRenderer',
            'NRenderer',
            'MRenderer',
            'LAcademicRenderer',
            'KRenderer',
            'JRenderer',
            'IRenderer',
            'HRenderer',
            'FRenderer',
            'DRenderer',
            'CRenderer',
            'ChRenderer',
            'CcRenderer',
            'BRenderer',
            'URenderer',
            'ORenderer',
            'ERenderer',
            'ARenderer',
        ],
        self::CLASSIC_ORTHOGRAPHY => [
            'SoftSignRenderer',
            'ZzRenderer',
            'ZRenderer',
            'YRenderer',
            'VRenderer',
            'UNonSyllabicRenderer',
            'TRenderer',
            'SsRenderer',
            'SRenderer',
            'RRenderer',
            'RRenderer',
            'PRenderer',
            'NRenderer',
            'MRenderer',
            'KRenderer',
            'JRenderer',
            'IRenderer',
            'HRenderer',
            'GRenderer',
            'FRenderer',
            'DRenderer',
            'CRenderer',
            'ChRenderer',
            'CcRenderer',
            'BRenderer',
            'URenderer',
            'ORenderer',
            'ERenderer',
            'ARenderer',
            'LClassicRenderer'
        ]
    ];

    /**
     * LatinConverter constructor.
     * @param string $orthography
     */
    public function __construct($orthography)
    {
        $this->setOrthography($orthography);
    }

    /**
     * @param string $orthography
     * @return $this
     * @throws \Exception
     */
    public function setOrthography($orthography)
    {
        $permittedOrthographies = [
            self::ACADEMIC_ORTHOGRAPHY,
            self::CLASSIC_ORTHOGRAPHY
        ];
        if ( ! in_array($orthography, $permittedOrthographies)) {
            throw new \Exception('The orthography is not permitted');
        }
        $this->orthography = $orthography;
        return $this;
    }

    /**
     * @param string $text
     * @return string
     */
    public function convert($text)
    {
        return $this->decorate()->render($text);
    }

    /**
     * @return DefaultRenderer
     */
    public function decorate()
    {
        $wrapper = app()->make(DefaultRenderer::class);
        foreach ($this->getRenderers() as $renderer) {
            $renderer = $this->getRendererInstance($renderer);
            $renderer->wrap($wrapper);
            $wrapper = $renderer;
        }
        return $wrapper;
    }

    /**
     * @return array
     */
    protected function getRenderers()
    {
        return $this->rendersMaps[$this->orthography];
    }

    /**
     * @param $name
     * @return DefaultRenderer
     */
    protected function getRendererInstance($name)
    {
        return app()->make(__NAMESPACE__ . '\LatinConverter\Renderers\\' . $name);
    }
}