<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<?php if ($this->item->booking_form) : ?>
  <div class="container">  
    <p class="pull-right">
      <img src="/images/general/logo-3.png" alt="French Connections"> 
    </p>
    <h1><?php echo JText::sprintf('COM_ACCOMMODATION_BOOKING_FORM_TITLE', $this->item->unit_title) ?></h1>
    <p class="lead">Print this form, fill it in and post to the address shown below.</p>
    <p>
      <?php echo ($this->item->firstname) ? $this->escape($this->item->firstname) : '' ?> 
      <?php echo ($this->item->surname) ? $this->escape($this->item->surname) . '<br />' : '' ?> 
      <?php echo ($this->item->address1) ? $this->escape($this->item->address1) . '<br />' : '' ?> 
      <?php echo ($this->item->address2) ? $this->escape($this->item->address2) . '<br />' : '' ?> 
      <?php echo ($this->item->city) ? $this->escape($this->item->city) . '<br />' : '' ?> 
      <?php echo ($this->item->region) ? $this->escape($this->item->region) . '<br />' : '' ?> 
      <?php echo ($this->item->postal_code) ? $this->escape($this->item->postal_code) . '<br />' : '' ?> 
      <?php echo ($this->item->country) ? $this->escape($this->item->country) . '<br />' : '' ?> 
    </p>
    <table class="table table-bordered">
      <tbody>
        <tr>
          <td colspan="2">
            Full name:
          </td>
        </tr>
        <tr>
          <td colspan="2">
            Address:
          </td>
        </tr>
        <tr>
          <td>
            Home telephone:
          </td>

          <td>
            Mobile telephone:
          </td>
        </tr>
        <tr>
          <td>
            Fax:
          </td>

          <td>
            Email:
          </td>
        </tr>
      </tbody>


      <button onclick="window.print()">Print</button>
    </table>
  </div>
<?php endif; ?>
