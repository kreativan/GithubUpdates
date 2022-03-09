<?php namespace ProcessWire;
/**
 *  Execute.php
 *  @param object $this_module
 *  @param string $page_name
 *  @author Ivan Milincic <kreativan.dev@gmail.com>
 *  @link http://kraetivan.dev
*/

?>

<table class="uk-table uk-table-small uk-table-middle uk-table-striped">
  <tbody>
    <tr>
      <td class="uk-width-medium">
        Status
        <?php if(!$this_module->isUpdateReady()) : ?>
          <span class="dot"></span>
        <?php endif; ?>
      </td>
      <td>
        <div id="ivm-status-message">
          <?php if($this_module->isUpdateReady()) :?>
            <b>v<?= $this_module->getUpdateInfo("version") ?></b> 
            update is ready to install
          <?php else : ?>
            No updates pending...
          <?php endif; ?>
        </div>
        <div id="ivm-downloading" class="uk-hidden">
          <i class="fa fa-cog fa-spin fa-lg"></i>
          <span class="loading-text">Downloading...</span>
        </div>
        <div id="ivm-checking" class="uk-hidden">
          <i class="fa fa-cog fa-spin fa-lg"></i>
          <span class="loading-text">Checking...</span>
        </div>
        <div id="ivm-installing" class="uk-hidden">
          <i class="fa fa-cog fa-spin fa-lg"></i>
          <span class="loading-text">Installing...</span>
        </div>
      </td>
    </tr>
    <tr>
      <td>Project</td>
      <td>
        <?= $this_module->vendor("project") ?>
      </td>
    </tr>
    <tr>
      <td>Version</td>
      <td>
        <?= $this_module->vendor("version") ?>
      </td>
    </tr>
    <tr>
      <td>Readme / Changelog</td>
      <td>
        <?php if($this_module->getReadme()):?>
          <a href="#readme-modal" uk-toggle>
            View
          </a>
        <?php else : ?>
          -
        <?php endif;?>
      </td>
    </tr>
    <tr>
      <td>Last Update</td>
      <td>
        <?= date("D, d M Y, h:s:1", $this_module->last_update) ?>
      </td>
    </tr>
  </tbody>
</table>

<div class="uk-margin">

  <?php if($this_module->isUpdateReady()) :?>
  <a href="./?install_updates=1" class="uk-button uk-button-primary uk-margin-small-right" onclick="installUpdates()">
    <i class="fa fa-cog uk-margin-small-right"></i>
    Install
  </a>
  <a href="./?delete_updates=1" class="uk-button uk-button-danger uk-margin-small-right"
    onclick="modalConfirm('Delete', 'Are you sure you want to discard pending updates?')">
    <i class="fa fa-trash uk-margin-small-right"></i>
    Delete
  </a>
  <?php endif;?>

  <a id="check-for-update" href="#" class="uk-button uk-button-secondary" onclick="runUpdateCheck()">
    <i class="fa fa-refresh uk-margin-small-right"></i>
    Check
  </a>

</div>

<?php if($this_module->isUpdateReady() && $this_module->getReadme()) :?>
<div id="readme-modal" uk-modal>
  <div class="uk-modal-dialog">

    <div class="uk-modal-header uk-background-muted uk-position-relative">
      <h3 class="uk-margin-remove uk-text-bold">README</h3>
      <button class="uk-modal-close uk-position-center-right uk-position-medium" type="button" uk-close></button>
    </div>

    <div class="uk-modal-body">
      <?= $this_module->getReadme() ?>
    </div>

  </div>
</div>
<?php endif;?>