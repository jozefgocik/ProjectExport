<?= $this->render('export/header', array('project' => $project, 'title' => $title)) ?>

<p class="alert alert-info"><?= t('This report contains all tasks information for the given date range.') ?></p>

<form class="js-modal-ignore-form" method="post"
  action="<?= $this->url->href('ProjectExportController',
                               'tasks', array('project_id' => $project['id'], 
                               'plugin' => 'ProjectExport')) ?>"
  autocomplete="off">
  <div class="form-top">
    <div class="form-left">
      <div class="checklist-row"><?= $this->form->checkbox('TaskId', t('ID'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('Title', t('Title'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('Description', t('Description'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('Column', t('Column'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('Status', t('Status'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('DueDate', t('Due date'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('CreationDate', t('Creation date'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('StartDate', t('Start date'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('TimeEstimated', t('Time estimated'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('TimeSpent', t('Time spent'), 1, true) ?></div>
    </div>

    <div class="form-right">
      <?= $this->form->csrf() ?>
      <?= $this->form->hidden('project_id', $values) ?>
      <?= $this->form->date(t('Start date'), 'from', $values) ?>
      <?= $this->form->date(t('End date'), 'to', $values) ?>
    </div>
  </div>
  <div>
    <div class="form-actions">
      <button type="submit" class="btn btn-blue js-form-export"><?= t('Export') ?></button>
      <?= t('or') ?>
      <?= $this->url->link(t('cancel'), 
                            'ProjectExportController',
                            'tasks', array('project_id' => $project['id'],
                            'plugin' => 'ProjectExport'),
                            false,
                            'js-modal-close') ?>
    </div>
  </div>
</form>
