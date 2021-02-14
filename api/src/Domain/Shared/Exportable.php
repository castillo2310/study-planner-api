<?php


namespace StudyPlanner\Domain\Shared;


/**
 * Interface Exportable
 * @package StudyPlanner\Domain\Shared
 */
interface Exportable
{
    /**
     * @param mixed $data
     * @return mixed
     */
    public function export($data);
}
