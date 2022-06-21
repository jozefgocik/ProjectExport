<?php

namespace Kanboard\Plugin\ProjectExport\Export;

use Kanboard\Core\Base;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\UserModel;

/**
 * Task Export
 *
 * @package  export
 * @author   Frederic Guillot
 */
class TaskExport extends Base
{
    /**
     * Fetch tasks and return the prepared CSV
     *
     * @access public
     * @param  integer $project_id Project id
     * @param  mixed   $from       Start date (timestamp or user formatted date)
     * @param  mixed   $to         End date (timestamp or user formatted date)
     * @return array
     */
    public function export($project_id, $from, $to, $id, $title, $description, $column, $status, $due_date, $creation_date, $start_date, $time_estimated, $time_spent, $swimlane, $category)
    {
        $tasks = $this->getTasks($project_id, $from, $to, $id, $title, $description, $column, $status, $due_date, $creation_date, $start_date, $time_estimated, $time_spent, $swimlane, $category);
        $taskIds = array_column($tasks, 'id');
        $tags = $this->taskTagModel->getTagsByTaskIds($taskIds);
        $colors = $this->colorModel->getList();
        $results = array($this->getColumns($id, $title, $description, $column, $status, $due_date, $creation_date, $start_date, $time_estimated, $time_spent, $swimlane, $category));

        foreach ($tasks as &$task) {
            $task = $this->format($task);
            $results[] = array_values($task);
        }

        return $results;
    }

    /**
     * Get the list of tasks for a given project and date range
     *
     * @access protected
     * @param  integer $project_id Project id
     * @param  mixed   $from       Start date (timestamp or user formatted date)
     * @param  mixed   $to         End date (timestamp or user formatted date)
     * @return array
     */
    protected function getTasks($project_id, $from, $to, $id, $title, $description, $column, $status, $due_date, $creation_date, $start_date, $time_estimated, $time_spent, $swimlane, $category)
    {
        if (!is_numeric($from)) {
            $from = $this->dateParser->removeTimeFromTimestamp($this->dateParser->getTimestamp($from));
        }

        if (!is_numeric($to)) {
            $to = $this->dateParser->removeTimeFromTimestamp(strtotime('+1 day', $this->dateParser->getTimestamp($to)));
        }

        $columnsCall = [];
        if ($id) {
            array_push($columnsCall, TaskModel::TABLE . '.id');
        }
        if ($title) {
            array_push($columnsCall, TaskModel::TABLE . '.title');
        }
        if ($swimlane) {
            array_push($columnsCall,  SwimlaneModel::TABLE . '.name AS swimlane_name');
        }
        if ($category) {
            array_push($columnsCall, CategoryModel::TABLE . '.name AS category_name');
        }
        if ($description) {
            array_push($columnsCall, TaskModel::TABLE . '.description');
        }
        if ($column) {
            array_push($columnsCall, ColumnModel::TABLE . '.title AS column_title');
        }
        if ($status) {
            array_push($columnsCall, TaskModel::TABLE . '.is_active');
        }
        if ($creation_date) {
            array_push($columnsCall, TaskModel::TABLE . '.date_creation');
        }
        if ($start_date) {
            array_push($columnsCall, TaskModel::TABLE . '.date_started');
        }
        if ($due_date) {
            array_push($columnsCall, TaskModel::TABLE . '.date_due');
        }
        if ($time_estimated) {
            array_push($columnsCall, TaskModel::TABLE . '.time_estimated');
        }
        if ($time_spent) {
            array_push($columnsCall, TaskModel::TABLE . '.time_spent');
        }

        return $this->db->table(TaskModel::TABLE)
            ->columns(...$columnsCall
            )
            ->join(UserModel::TABLE, 'id', 'owner_id', TaskModel::TABLE)
            ->left(UserModel::TABLE, 'uc', 'id', TaskModel::TABLE, 'creator_id')
            ->join(CategoryModel::TABLE, 'id', 'category_id', TaskModel::TABLE)
            ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
            ->join(SwimlaneModel::TABLE, 'id', 'swimlane_id', TaskModel::TABLE)
            ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE)
            ->gte(TaskModel::TABLE . '.date_creation', $from)
            ->lte(TaskModel::TABLE . '.date_creation', $to)
            ->eq(TaskModel::TABLE . '.project_id', $project_id)
            ->asc(TaskModel::TABLE . '.id')
            ->findAll();
    }

    /**
     * Format the output of a task array
     *
     * @access protected
     * @param  array  $task
     * @param  array  $colors
     * @param  array  $tags
     * @return array
     */
    protected function format(array &$task)
    {
        if ($task['is_active'] != '') {
            $task['is_active'] = $task['is_active'] == TaskModel::STATUS_OPEN ? e('Open') : e('Closed');
        }

        $task = $this->dateParser->format(
            $task,
            array('date_due', 'date_modification', 'date_creation', 'date_started', 'date_completed'),
            $this->dateParser->getUserDateTimeFormat()
        );

        return $task;
    }

    /**
     * Get column titles
     *
     * @access protected
     * @return string[]
     */
    protected function getColumns($id, $title, $description, $column, $status, $due_date, $creation_date, $start_date, $time_estimated, $time_spent, $swimlane, $category)
    {
        $columns = [];

        if ($id) {
            array_push($columns, e('Task Id'));
        }
        if ($title) {
            array_push($columns, e('Title'));
        }
        if ($swimlane) {
            array_push($columns, e('Swimlane'));
        }
        if ($category) {
            array_push($columns, e('Category'));
        }
        if ($description) {
            array_push($columns, e('Description'));
        }
        if ($column) {
            array_push($columns, e('Column'));
        }
        if ($status) {
            array_push($columns, e('Status'));
        }
        if ($creation_date) {
            array_push($columns, e('Creation date'));
        }
        if ($start_date) {
            array_push($columns, e('Start date'));
        }
        if ($due_date) {
            array_push($columns, e('Due date'));
        }
        if ($time_estimated) {
            array_push($columns, e('Time estimated'));
        }
        if ($time_spent) {
            array_push($columns, e('Time spent'));
        }

        return array(...$columns);
    }
}
