<!-- TODO: move into a css file -->
{literal}
<style>
	input.inactive { background: #bbbbbb; }
	input.changed { background: #ffb2b2; }
</style>
{/literal}
{* implicit input: $tag, $extra_attributes *}
{def $attributeValues = tag_get_extra_attributes($tag.id)}
<div class="controlbar"><div class="box-bc"><div class="box-ml">
	<fieldset>
		<legend>Tag Meta Data</legend>
		<span class="attribute_errors"></span>
		<table class="list">
			<tbody>
				<tr><th>Name</th><th>Value</th><th></th></tr>
				{foreach $extra_attributes as $attributeName => $attributeLabel}
					<tr>
						<td>{$attributeLabel}</td>
						<td>
							<input type="text" name="attribute_{$attributeName}" id="attribute_{$attributeName}" value="{$attributeValues[$attributeName]}" oninput="valueChanged('{$attributeName}')" onchange="fullValueChanged('{$attributeName}')" />
						</td>
						<td>
							<input type="button" value="Submit" id="write_{$attributeName}" onclick="writeAttribute('{$attributeName}')" class="button inactive" disabled="disabled" />
							<input type="button" value="Clear" id="clear_{$attributeName}" onclick="clearAttribute('{$attributeName}')" class="button {if $attributeValues[$attributeName]|eq("")}inactive{/if}" {if $attributeValues[$attributeName]|eq("")}disabled="disabled"{/if} />
						</td>
					</tr>
				{/foreach}
		</table>
	</fieldset>
</div></div></div>

{literal}
<script language="javascript">
	function valueChanged(attributeName) {

		// enable write, always, something has changed, change input bg to red
		$("#write_"+attributeName).prop("disabled", false).removeClass("inactive");
		$("#attribute_"+attributeName).addClass("changed");

		if ($("#attribute_"+attributeName).val() != "") { // enable clear
			$("#clear_"+attributeName).prop("disabled", false).removeClass("inactive");
		} else {
			$("#clear_"+attributeName).prop("disabled", "disabled").addClass("inactive");
		}
	}

	function fullValueChanged(attributeName) {
		// TODO: autosave if enabled with ini
	}

	function writeAttribute(attributeName) {
		var dataSent = {
			tagId: {/literal}{$tag.id}{literal},
			attributeName: attributeName, 
			attributeValue: $("#attribute_"+attributeName).val()
		};
		$.ez( 'eztagmetadata::writeAttribute', dataSent, function (ezp_data) {
			if ( ezp_data.error_text ) { // pop up a  fading message
				$(".attribute_errors").html("Error writing your attribute... Have you added it to the database? (see documentation)").show(fast).hide(3000);
			} else { // good here, deactivate two buttons, and return out item to non-edited state
				$("#write_"+attributeName).prop("disabled", true).addClass("inactive");
				$("#attribute_"+attributeName).removeClass("changed");
			}
		});
	}

	function clearAttribute(attributeName) {
		var valBefore = $("#attribute_"+attributeName).val();

		if (valBefore != "") {
			$("#attribute_"+attributeName).val("");
			$("#attribute_"+attributeName).addClass("changed");
			$("#write_"+attributeName).prop("disabled", false).removeClass("inactive");
			$("#clear_"+attributeName).prop("disabled", true).addClass("inactive");
		}
	}
</script>
{/literal}