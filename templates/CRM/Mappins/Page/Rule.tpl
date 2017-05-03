{if $action eq 1 or $action eq 2 or $action eq 4 or $action eq 8}
   {include file="CRM/Mappins/Form/Rule.tpl"}
{else}
<div class="help">
  <p>{ts}Drag rows to change the order.{/ts}</p>
  <p>{ts}Each mapped contact is checked against each rule, in the order shown here, top to bottom. The pin image of the first matching rule is used; if no rule matches, CiviCRM's default pin image is used.{/ts}</p>
</div>
{if $rows}
{if !($action eq 1 and $action eq 2)}
    <div class="action-link">
      {crmButton q="action=add&reset=1" class="newMappinsRule" icon="plus-circle"}{ts}Add Map Pins Rule{/ts}{/crmButton}
    </div>
{/if}

<div id="ltype">

    {strip}
  {* handle enable/disable actions*}
  {include file="CRM/common/enableDisableApi.tpl"}
    <table id="options" class="display">
    <thead>
    <tr>
      <th>{ts}ID{/ts}</th>
      <th>{ts}Criteria{/ts}</th>
      <th>{ts}Value{/ts}</th>
      <th>{ts}Image{/ts}</th>
      <th>{ts}Enabled?{/ts}</th>
      <th></th>
    </tr>
    </thead>    
    <tbody>
      {foreach from=$rows item=row}
      <tr id="mappins_rule-{$row.id}" class="crm-entity {cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if} crm-mappinsrule">
          <td class="crm-mappinsrule" data-field="id">{$row.id}</td>
          <td class="crm-mappinsrule" data-field="criteria">{$row.criteria}</td>
          <td class="crm-mappinsrule-value crm-editable" data-field="value">{$row.value}</td>
          <td><img class="crm-mappins-rule-image" id="crm-mappinsrule-image-{$row.id}" src="{$row.image_url}" height="20px" width="20px" /></td>
          <td class="crm-mappinsrule-is_active" id="row_{$row.id}_status">{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
          <td>{$row.action|replace:'xx':$row.id}</td>
      </tr>
      {/foreach}
    </tbody>
    </table>
    {/strip}
</div>
{else}
    <div class="messages status no-popup">
      <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/>
      {ts}None found.{/ts}
    </div>
{/if}
  <div class="action-link">
    {crmButton q="action=add&reset=1" class="newMappinsRule" icon="plus-circle"}{ts}Add Map Pins Rule{/ts}{/crmButton}
  </div>
{/if}
