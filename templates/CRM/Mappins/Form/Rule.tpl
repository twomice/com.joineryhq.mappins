{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2017                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{* this template is used for adding/editing mappins rules  *}
<div class="crm-block crm-form-block crm-relationship-type-form-block">
    <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
  {if $action eq 8}
    <div class="messages status no-popup">
      <div class="icon inform-icon"></div>
      {ts 1=$id}Permanently delete Map Pins Rule %1?{/ts}
    </div>
  {else}
      <table class="form-layout-compressed">
        {if $id}
          <tr class="crm-relationship-type-form-block-id">
              <td class="label">ID</td>
              <td>{$id}</td>
          </tr>
        {/if}
        <tr class="crm-relationship-type-form-block-criteria">
            <td class="label">{$form.criteria.label}</td>
            <td>{$form.criteria.html}<br />
            <span class="description">{ts}The attribute of the mapped contact to check.{/ts}</span></td>
        </tr>
        <tr class="crm-relationship-type-form-block-value">
            <td class="label">{$form.value.label}</td>
            <td>{$form.value.html}<br />
            <span class="description">{ts}The value which the mapped contact must have, for the given criteria, in order for this rule to match.{/ts}</span></td>
        </tr>
        <tr class="crm-relationship-type-form-block-image_url" id="image_url_tr">
            <td class="label">{$form.image_url.label}</td>            
            <td>
              <a class="button crm-mappins-rule-image-button" id="mappinsrule-image-button" href="#" data-rule-id="{$row.id}">
                <img id="mappinsrule-image-preview" src="{$image_url}" height="20px" width="20px" />
              </a>
                <span style="">{$form.image_url.html}</span>
              <br style="clear:both" />
              <span class="description">{ts}The image to use if this rule matches. Click to select/upload an image.{/ts}</span>
              </td>
            </td>              
              
        </tr>
        <tr class="crm-relationship-type-form-block-uf_group_id">
            <td class="label">{$form.uf_group_id.label}</td>
            <td>{$form.uf_group_id.html}<br />
            <span class="description">{ts}uf_group_id{/ts}</span></td>
        </tr>
        <tr class="crm-relationship-type-form-block-is_active">
            <td class="label">{$form.is_active.label}</td>
            <td>{$form.is_active.html}<br />
            <span class="description">{ts}Is this rule active?{/ts}</span></td>
        </tr>
      </table>
    {/if}
    <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>