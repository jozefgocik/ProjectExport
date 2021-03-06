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
      <div class="checklist-row"><?= $this->form->checkbox('Swimlane', t('Swimlane'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('Category', t('Category'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('Description', t('Description'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('Column', t('Column'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('Status', t('Status'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('DueDate', t('Due date'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('CreationDate', t('Creation date'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('StartDate', t('Start date'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('Salary', t('Salary'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('TimeEstimated', t('Time estimated'), 1, true) ?></div>
      <div class="checklist-row"><?= $this->form->checkbox('TimeSpent', t('Time spent'), 1, true) ?></div>
    </div>

    <div class="form-right">
      <?= $this->form->csrf() ?>
      <?= $this->form->hidden('project_id', $values) ?>
      <?= $this->form->date(t('Start date'), 'from', $values) ?>
      <?= $this->form->date(t('End date'), 'to', $values) ?>

      <form>
        <label>Sort table by:</label>
        <label>
            <input type="radio" name="sort_table_by" value="sort_by_id"> ID
        </label>
        <label>
            <input type="radio" name="sort_table_by" value="sort_by_swimlane"> Swimlane
        </label>
        <label>
            <input type="radio" name="sort_table_by" value="sort_by_category"> Category
        </label>
      </form>
    </div>
    <div class="form-right">
      <div>
          <label for="head">Table header background color:</label>
          <input type="color" id="header_bg" name="header_bg" value="#36304a">
          <label for="head">Table header text color:</label>
          <input type="color" id="header_text" name="header_text" value="#ffffff">
      </div>
      <div>
          <label for="body">Table body background color:</label>
          <input type="color" id="body_bg" name="body_bg" value="#f5f5f5">
          <label for="body">Table body text color:</label>
          <input type="color" id="body_text" name="body_text" value="#808080">
      </div>
      <div>
          <label for="body">Table footer background color:</label>
          <input type="color" id="footer_bg" name="footer_bg" value="#cccccc">     
          <label for="body">Table footer text color:</label>
          <input type="color" id="footer_text" name="footer_text" value="#000000">      
      </div>
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
