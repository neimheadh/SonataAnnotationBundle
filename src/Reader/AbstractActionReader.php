<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Reader;

use Doctrine\Common\Annotations\Reader;
use Exception;
use Neimheadh\SonataAnnotationBundle\Annotation\AbstractAction;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use ReflectionClass;

/**
 * Action reader main class.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
abstract class AbstractActionReader extends AbstractReader
{

    /**
     * Associated annotation class.
     *
     * @var string|null
     */
    protected ?string $annotationClass;

    /**
     * @param Reader  $annotationReader Doctrine annotation reader.
     * @param ?string $annotationClass  Associated annotation class.
     */
    public function __construct(
        Reader $annotationReader,
        ?string $annotationClass = null
    ) {
        parent::__construct($annotationReader);
        $this->annotationClass = $annotationClass;
    }

    /**
     * Get action list.
     *
     * @param ReflectionClass $class   Entity class.
     * @param array           $actions Action list.
     *
     * @return array
     * @throws MissingAnnotationArgumentException if a template attribute is
     *                                            missing.
     * @throws Exception if an appropriate source of randomness cannot be
     *                   found.
     */
    public function getActions(ReflectionClass $class, array $actions): array
    {
        foreach ($this->getClassAnnotations($class) as $annotation) {
            if ($this->isSupported($annotation)) {
                if (!isset($annotation->template)) {
                    throw new MissingAnnotationArgumentException(
                        $annotation,
                        'template',
                        $class
                    );
                }

                /** @var AbstractAction $annotation */
                $actions[random_int(
                    -99999,
                    99999
                )]['template'] = $annotation->template;
            }
        }

        return $actions;
    }

    /**
     * Is the given annotation supported.
     *
     * @param object $annotation Annotation.
     *
     * @return bool
     */
    protected function isSupported(object $annotation): bool
    {
        return $annotation instanceof $this->annotationClass;
    }

}
