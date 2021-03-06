<?php

namespace Kanboard\Plugin\ProjectExport\Controller;

use Kanboard\Controller\BaseController;

/**
 * Export Controller
 *
 * @package  Kanboard\Plugin\ProjectExport\Controller
 * @author   Matej Kovaľ
 */
class ProjectExportController extends BaseController
{

    private function common($model, $method, $filename, $action, $page_title)
    {
        $project = $this->getProject();

        if ($this->request->isPost()) {
            $from = $this->request->getRawValue('from'); // Get data from request
            $to = $this->request->getRawValue('to');

            $id = $this->request->getRawValue('TaskId');
            $title = $this->request->getRawValue('Title');
            $swimlane = $this->request->getRawValue('Swimlane');
            $category = $this->request->getRawValue('Category');
            $description = $this->request->getRawValue('Description');
            $salary = $this->request->getRawValue('Salary');
            $column = $this->request->getRawValue('Column');
            $status = $this->request->getRawValue('Status');
            $due_date = $this->request->getRawValue('DueDate');
            $creation_date = $this->request->getRawValue('CreationDate');
            $start_date = $this->request->getRawValue('StartDate');
            $time_estimated = $this->request->getRawValue('TimeEstimated');
            $time_spent = $this->request->getRawValue('TimeSpent');
            //Colors
            $color_header_bg = $this->request->getRawValue('header_bg');
            $color_header_text = $this->request->getRawValue('header_text');
            $color_body_bg = $this->request->getRawValue('body_bg');
            $color_body_text = $this->request->getRawValue('body_text');
            $color_footer_bg = $this->request->getRawValue('footer_bg');
            $color_footer_text = $this->request->getRawValue('footer_text');

            if ($from && $to) {
                $data = $this->$model->$method($project['id'], $from, $to, $id, $title, $description, $column, $status, $due_date, $creation_date, $start_date, $time_estimated, $time_spent, $swimlane, $category, $salary);

                $table = "";
                $styles = "
                  <style>
                    .export-table {
                      border-collapse: collapse;
                      text-align: center;
                      font-family: 'Arial';
                      width: 100%;
                      table-layout: auto;
                    }

                    .export-table thead tr {
                      background: $color_header_bg;
                      font-size: 17px;
                      color: $color_header_text;
                    }

                    .export-table tr {
                      font-size: 15px;
                      color: $color_body_text;
                    }

                    .export-table td, .export-table th {
                      padding: 1em 0;
                      max-width: 400px;
                    }

                    .export-table tr:nth-child(2n) {
                      background: $color_body_bg;
                    }

                    .sum-cell {
                      background: $color_footer_bg;
                      color: $color_footer_text;
                    }
                  </style>";
                $i = 0; // For identifying first row
                $hoursIndex = 0; // Index of column with hours
                $estimatedHoursIndex = 0; // Index of column with estimated hours
                $salaryIndex = 0;
                $creationDateIndex = 0;
                $startDateIndex = 0;
                $dueDateIndex = 0;
                $sumHours = 0.0; // Sums of Done tasks
                $sumEstimated = 0.0;
                $sumSalary = 0.0;
                $hoursIndexFound = false;
                $estimatedHoursIndexFound = false;
                $salaryIndexFound = false;

                print "<img src='/kanboard/plugins/ProjectExport/revolware_logo.png' class='center' alt='Revolware Logo' width='30%' style='display: block; margin-left: auto; margin-right: auto;'>";

                $projectName = $project['name'];
                print "<h1 style='text-align: center; font-family:Arial;'>$projectName</h1>";

                $projectDescription = $project['description'];

                if (empty($projectDescription)) {
                    $this->response->json(array());
                } else {
                    $preview = $this->helper->text->markdown($projectDescription);
                    print("<div style='font-family:Arial'>$preview</div>");
                }

                foreach ($data as $row) {
                    $done = false; // For identifying if this row is in column Done
                    $j = 0; // For identifying cell in row
                    if ($i == 0) {
                        $table .= "<thead>";
                    }

                    $table .= "<tr>";
                    foreach ($row as $cell) {
                        if ($i == 0) { // For finding indices of columns
                            if ($cell == "Time spent") {
                                $hoursIndex = $j;
                                $hoursIndexFound = true;
                            }
                            if ($cell == "Time estimated") {
                                $estimatedHoursIndex = $j;
                                $estimatedHoursIndexFound = true;
                            }
                            if ($cell == "Salary") {
                                $salaryIndex = $j;
                                $salaryIndexFound = true;
                            }
                            if ($cell == "Creation date") {
                                $creationDateIndex = $j;
                            }
                            if ($cell == "Start date") {
                                $startDateIndex = $j;
                            }
                            if ($cell == "Due date") {
                                $dueDateIndex = $j;
                            }
                            $table .= "<th>" . $cell . "</th> ";
                        } else {
                            if ($cell == "Done" || $cell == "Finished") { // Check wheter task is in Done
                                $done = true;
                            } // Formats date from date columns from Y-m-d H-m-s to d-m-Y
                            if ((($creationDateIndex != 0 && $j == $creationDateIndex) 
                                  || ($startDateIndex != 0 && $j == $startDateIndex) 
                                  || $dueDateIndex != 0 && $j == $dueDateIndex) && $j != 0) {
                                $date = date_create($cell);
                                $table .= "<td>" . date_format($date, "d-m-Y") . "</td> ";
                            } else {
                                $table .= "<td>" . $cell . "</td> ";
                            }
                        }

                        if ($hoursIndex != 0 && $j == $hoursIndex && $j != 0) {
                            $sumHours += floatval($cell);
                        }
                        if ($estimatedHoursIndex != 0 && $j == $estimatedHoursIndex && $j != 0) {
                            $sumEstimated += floatval($cell);
                        }
                        if ($salaryIndex != 0 && $j == $salaryIndex && $j != 0) {
                            $sumSalary += floatval($cell);
                        }

                        $j++;
                    }

                    $table .= "</tr>";

                    if ($i == 0) {
                        $table .= "</thead>";
                    }
                    $i++;
                }

                $sumRow = "<tr>"; // Code for sum row
                if ($salaryIndexFound) {
                    for ($a = 0; $a < $salaryIndex; $a++) {
                        $sumRow .= "<td></td>";
                    }
                }
                else if ($estimatedHoursIndexFound) {
                    for ($a = 0; $a < $estimatedHoursIndex; $a++) {
                        $sumRow .= "<td></td>";
                    }
                }
                else if ($hoursIndexFound) {
                    for ($a = 0; $a < $hoursIndex; $a++) {
                        $sumRow .= "<td></td>";
                    }
                }

                if ($salaryIndexFound) {
                    $sumRow .= "<td class='sum-cell'>Sum: <b>" . $sumSalary . " €" . "</b></td>";
                }
                if ($estimatedHoursIndexFound) {
                    $sumRow .= "<td class='sum-cell'>Sum: <b>" . $sumEstimated . "</b></td>";
                }
                if ($hoursIndexFound) {
                    $sumRow .= "<td class='sum-cell'>Sum: <b>" . $sumHours . "</b></td></tr>";
                }
                if (!$hoursIndexFound && !$estimatedHoursIndexFound && !$salaryIndexFound) {
                    $sumRow = "";
                }

                $this->response->html( // Final table
                    "<!DOCTYPE html><html><head>" . $styles . "<meta charset='UTF-8'></head><body><table class='export-table'>" .
                    $table . $sumRow
                    . "</table></body>"
                );

                //$this->response->withFileDownload($filename.'.csv');
                //$this->response->csv($data);
            }
        } else {
            $this->response->html($this->template->render('export/' . $action, array(
                'values' => array(
                    'project_id' => $project['id'],
                    'from' => '',
                    'to' => '',
                ),
                'errors' => array(),
                'project' => $project,
                'title' => $page_title,
            )));
        }
    }

    /**
     * Task export
     *
     * @access public
     */
    public function tasks()
    {
        $this->common('taskExport', 'export', t('Tasks'), 'tasks', t('Tasks Export'));
    }

    /**
     * Subtask export
     *
     * @access public
     */
    public function subtasks()
    {
        $this->common('subtaskExport', 'export', t('Subtasks'), 'subtasks', t('Subtasks Export'));
    }

    /**
     * Daily project summary export
     *
     * @access public
     */
    public function summary()
    {
        $project = $this->getProject();

        if ($this->request->isPost()) {
            $from = $this->request->getRawValue('from');
            $to = $this->request->getRawValue('to');

            if ($from && $to) {
                $from = $this->dateParser->getIsoDate($from);
                $to = $this->dateParser->getIsoDate($to);
                $data = $this->projectDailyColumnStatsModel->getAggregatedMetrics($project['id'], $from, $to);
                $this->response->withFileDownload(t('Summary') . '.csv');
                $this->response->csv($data);
            }
        } else {
            $this->response->html($this->template->render('export/summary', array(
                'values' => array(
                    'project_id' => $project['id'],
                    'from' => '',
                    'to' => '',
                ),
                'errors' => array(),
                'project' => $project,
                'title' => t('Daily project summary export'),
            )));
        }
    }

    /**
     * Transition export
     *
     * @access public
     */
    public function transitions()
    {
        $this->common('transitionExport', 'export', t('Transitions'), 'transitions', t('Task transitions export'));
    }
}
