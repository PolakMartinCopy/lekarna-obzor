<?php
if (isset($this->params['named']['tab'])) {
	$tab_pos = $this->params['named']['tab'] - 1;
?>
	<script>
		$(function() {
			$( "#tabs" ).tabs("option", "selected", <?php echo $tab_pos?>);
		});
	</script>
<?php } ?>


<h1>Detail obchodního jednání</h1>

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Info</a></li>
		<li><a href="#tabs-2">Přizvané KO</a></li>
		<li><a href="#tabs-3">Náklady</a></li>
		<li><a href="#tabs-4">Nabídky</a></li>
	</ul>
		
<?php /* TAB 1 ****************************************************************************************************************/ ?>
	<div id="tabs-1">
		<h2>Základní informace</h2>
		<table class="left_heading">
			<tr>
				<th>ID</th>
				<td><?php echo $business_session['BusinessSession']['id']?></td>
			</tr>
			<tr>
				<th>Obchodní partner</th>
				<td><?php echo $html->link($business_session['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $business_session['BusinessPartner']['id']))?></td>
			</tr>
			<tr>
				<th>Uživatel</th>
				<td><?php echo $business_session['User']['first_name'] . ' ' . $business_session['User']['last_name']?></td>
			</tr>
			<tr>
				<th>Datum uskutečnění</th>
				<td><?php echo $business_session['BusinessSession']['date']?></td>
			</tr>
			<tr>
				<th>Datum vložení</th>
				<td><?php echo $business_session['BusinessSession']['created']?></td>
			</td>
			<tr>
				<th>Typ jednání</th>
				<td><?php echo $business_session['BusinessSessionType']['name']?></td>
			</tr>
			<tr>
				<th>Stav jednání</th>
				<td><?php echo $business_session['BusinessSessionState']['name']?></td>
			</tr>
			<tr>
				<th>Popis</th>
				<td><?php echo $business_session['BusinessSession']['description']?></td>
			</tr>
			<tr>
				<th>Přizvaní uživatelé</th>
				<td>
		<?php		if (!empty($business_session['BusinessSessionsUser'])) { ?>
					<ul>
		<?php 			foreach ($business_session['BusinessSessionsUser'] as $user) {?>
						<li><?php echo $user['User']['first_name'] . ' ' . $user['User']['last_name']?></li>
		<?php 			} ?>
					</ul>
		<?php 		}?>
				</td>
			</tr>
		</table>
	</div>
	
<?php /* TAB 2 ****************************************************************************************************************/ ?>
	<div id="tabs-2">
		<h2>Přizvané kontaktní osoby</h2>
		<button id="search_form_show_contact_people">vyhledávací formulář</button>
		<?php
			$hide = ' style="display:none"';
			if ( isset($this->data['ContactPersonSearch2']) ){
				$hide = '';
			}
		?>
		<div id="search_form_contact_people"<?php echo $hide?>>
			<?php echo $form->create('ContactPerson', array('url' => array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id'], 'tab' => 2))); ?>
			<table class="left_heading">
				<tr>
					<th>Titul</th>
					<td><?php echo $form->input('ContactPersonSearch2.ContactPerson.prefix', array('label' => false))?></td>
					<th>Jméno</th>
					<td><?php echo $form->input('ContactPersonSearch2.ContactPerson.first_name', array('label' => false))?></td>
					<th>Příjmení</th>
					<td><?php echo $form->input('ContactPersonSearch2.ContactPerson.last_name', array('label' => false))?></td>
				</tr>
				<tr>
					<th>Telefon</th>
					<td><?php echo $form->input('ContactPersonSearch2.ContactPerson.phone', array('label' => false))?></td>
					<th>Mobil</th>
					<td><?php echo $form->input('ContactPersonSearch2.ContactPerson.cellular', array('label' => false))?></td>
					<th>Email</th>
					<td><?php echo $form->input('ContactPersonSearch2.ContactPerson.email', array('label' => false))?></td>
				</tr>
				<tr>
					<th>Obchodní partner</th>
					<td><?php echo $form->input('ContactPersonSearch2.BusinessPartner.name', array('label' => false))?></td>
					<td colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="6">
						<?php echo $html->link('reset filtru', array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id'], 'reset' => 'contact_people')) ?>
					</td>
				</tr>
			</table>
			<?php
				echo $form->hidden('ContactPersonSearch2.ContactPerson.search_form', array('value' => 1));
				echo $form->submit('Vyhledávat');
				echo $form->end();
			?>
		</div>
		
		<script>
			$("#search_form_show_contact_people").click(function () {
				if ($('#search_form_contact_people').css('display') == "none"){
					$("#search_form_contact_people").show("slow");
				} else {
					$("#search_form_contact_people").hide("slow");
				}
			});
		</script>
		<?php 
		echo $form->create('CSV', array('url' => array('controller' => 'business_sessions_contact_people', 'action' => 'xls_export')));
		echo $form->hidden('data', array('value' => serialize($invited_contact_people_find)));
		echo $form->hidden('fields', array('value' => serialize($invited_contact_people_export_fields)));
		echo $form->submit('CSV');
		echo $form->end();

		if (empty($contact_people)) {
		?>
		<p><em>Na toto obchodní jednání nejsou přizvány žádné kontaktní osoby</em>.</p>
		<?php } else { ?>
		<table class="top_heading">
			<tr>
				<th>ID</th>
				<th>Křestní jméno</th>
				<th>Příjmení</th>
				<th>Titul</th>
				<th>Telefon</th>
				<th>Mobilní telefon</th>
				<th>Email</th>
				<th>Obchodní partner</th>
				<th>&nbsp;</th>
			</tr>
		<?php
			$odd = '';
			foreach ($contact_people as $contact_person) {
				$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
		?>
			<tr<?php echo $odd?>>
				<td><?php echo $contact_person['ContactPerson']['id']?></td>
				<td><?php echo $contact_person['ContactPerson']['first_name']?></td>
				<td><?php echo $contact_person['ContactPerson']['last_name']?></td>
				<td><?php echo $contact_person['ContactPerson']['prefix']?></td>
				<td><?php echo $contact_person['ContactPerson']['phone']?></td>
				<td><?php echo $contact_person['ContactPerson']['cellular']?></td>
				<td><?php echo $html->link($contact_person['ContactPerson']['email'], 'mailto:' . $contact_person['ContactPerson']['email'])?></td>
				<td><?php echo $html->link($contact_person['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $contact_person['BusinessPartner']['id']))?></td>
				<td>
					<?php echo $html->link('Upravit', array('controller' => 'contact_people', 'action' => 'edit', $contact_person['ContactPerson']['id']))?>
					<?php echo $html->link('Smazat', array('controller' => 'contact_people', 'action' => 'delete', $contact_person['ContactPerson']['id']))?>
				</td>
			</tr>
		<?php } ?>
		</table>
		<?php } ?>
		<?php echo $html->link('Přizvat kontaktní osoby', array('controller' => 'business_sessions', 'action' => 'invite', $business_session['BusinessSession']['id']))?>
	</div>
	
<?php /* TAB 3 ****************************************************************************************************************/ ?>
	<div id="tabs-3">
		<h2>Náklady</h2>
		<button id="search_form_show_costs">vyhledávací formulář</button>
		<?php
			$hide = ' style="display:none"';
			if ( isset($this->data['CostForm']) ){
				$hide = '';
			}
		?>
		<div id="search_form_costs"<?php echo $hide?>>
			<?php echo $form->create('Cost', array('url' => array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id'], 'tab' => 3))); ?>
			<table class="left_heading">
				<tr>
					<th>Datum</th>
					<td><?php echo $form->input('CostForm.Cost.date', array('label' => false, 'type' => 'text'))?></td>
					<th>Popis</th>
					<td><?php echo $form->input('CostForm.Cost.description', array('label' => false, 'type' => 'text'))?></td>
				</tr>
				<tr>
					<th>Částka od</th>
					<td><?php echo $form->input('CostForm.Cost.amount_from', array('label' => false))?></td>
					<th>Částka do</th>
					<td><?php echo $form->input('CostForm.Cost.amount_to', array('label' => false))?></td>
				</tr>
				<tr>
					<td colspan="4"><?php echo $html->link('reset filtru', array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id'], 'reset:costs'))?></td>
				</tr>
			</table>
			<?php
				echo $form->hidden('CostForm.Cost.search_form', array('value' => 1));
				echo $form->submit('Vyhledávat');
				echo $form->end();
			?>
		</div>
		
		<script>
			$("#search_form_show_costs").click(function () {
				if ($('#search_form_costs').css('display') == "none"){
					$("#search_form_costs").show("slow");
				} else {
					$("#search_form_costs").hide("slow");
				}
			});
		
			$(function() {
				var dates = $( "#CostFormCostDate" ).datepicker({
					defaultDate: "+1w",
					changeMonth: false,
					numberOfMonths: 1,
					onSelect: function( selectedDate ) {
						var option = this.id == "CostFormCostDate" ? "minDate" : "maxDate",
							instance = $( this ).data( "datepicker" ),
							date = $.datepicker.parseDate(
								instance.settings.dateFormat ||
								$.datepicker._defaults.dateFormat,
								selectedDate, instance.settings );
						dates.not( this ).datepicker( "option", option, date );
					}
				});
			});
			$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
		</script>
		<?php
		echo $form->create('CSV', array('url' => array('controller' => 'costs', 'action' => 'xls_export')));
		echo $form->hidden('data', array('value' => serialize($costs_find)));
		echo $form->hidden('fields', array('value' => serialize($costs_export_fields)));
		echo $form->submit('CSV');
		echo $form->end();
		
		if (empty($business_session['Cost'])) {
		?>
		<p><em>K tomuto jednání se nevztahují žádné náklady.</em></p>
		<?php } else { ?>
		<table class="top_heading">
			<tr>
				<th>ID</th>
				<th>Datum</th>
				<th>Částka</th>
				<th>Popis</th>
				<th>&nbsp;</th>
			</tr>
		<?php
			$odd = '';
			foreach ($business_session['Cost'] as $cost) {
				$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
		?>
			<tr<?php echo $odd?>>
				<td><?php echo $cost['id']?></td>
				<td><?php echo $cost['date']?></td>
				<td><?php echo $cost['amount']?></td>
				<td><?php echo (strlen($cost['description']) <= 50 ? $cost['description'] : substr($cost['description'], 0, 50) . '...') ?></td>
				<td class="actions">
					<?php echo $html->link('Detail', array('controller' => 'costs', 'action' => 'view', $cost['id']))?>
					<?php echo $html->link('Upravit', array('controller' => 'costs', 'action' => 'edit', $cost['id']))?>
					<?php echo $html->link('Smazat', array('controller' => 'costs', 'action' => 'delete', $cost['id']), null, 'Opravdu si přejete náklad smazat?')?>
				</td>
			</tr>
		<?php } ?>
		</table>
		<?php } ?>
		<ul>
			<li><?php echo $html->link('Přidat náklady', array('controller' => 'costs', 'action' => 'add', 'business_session_id' => $business_session['BusinessSession']['id']))?></li>
		</ul>
	</div>
	
<?php /* TAB 4 ****************************************************************************************************************/ ?>
	<div id="tabs-4">
		<h2>Nabídky</h2>
		<button id="search_form_show_offers">vyhledávací formulář</button>
		<?php
			$hide = ' style="display:none"';
			if ( isset($this->data['OfferForm']) ){
				$hide = '';
			}
		?>
		<div id="search_form_offers"<?php echo $hide?>>
			<?php echo $form->create('Offer', array('url' => array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id'], 'tab' => 4))); ?>
			<table class="left_heading">
				<tr>
					<th>Datum vytvoření</th>
					<td><?php echo $form->input('OfferForm.Offer.created', array('label' => false, 'type' => 'text'))?></td>
					<th>Obsah</th>
					<td><?php echo $form->input('OfferForm.Offer.content', array('label' => false, 'type' => 'text'))?></td>
				</tr>
				<tr>
					<td colspan="4"><?php echo $html->link('reset filtru', array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id'], 'reset' => 'offers'))?></td>
				</tr>
			</table>
			<?php
				echo $form->hidden('OfferForm.Offer.search_form', array('value' => 1));
				echo $form->submit('Vyhledávat');
				echo $form->end();
			?>
		</div>
		
		<script>
			$("#search_form_show_offers").click(function () {
				if ($('#search_form_offers').css('display') == "none"){
					$("#search_form_offers").show("slow");
				} else {
					$("#search_form_offers").hide("slow");
				}
			});
		
			$(function() {
				var dates = $( "#OfferFormOfferCreated" ).datepicker({
					defaultDate: "+1w",
					changeMonth: false,
					numberOfMonths: 1,
					onSelect: function( selectedDate ) {
						var option = this.id == "OfferFormOfferCreated" ? "minDate" : "maxDate",
							instance = $( this ).data( "datepicker" ),
							date = $.datepicker.parseDate(
								instance.settings.dateFormat ||
								$.datepicker._defaults.dateFormat,
								selectedDate, instance.settings );
						dates.not( this ).datepicker( "option", option, date );
					}
				});
			});
			$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
		</script>
		<?php
		echo $form->create('CSV', array('url' => array('controller' => 'offers', 'action' => 'xls_export')));
		echo $form->hidden('data', array('value' => serialize($offers_find)));
		echo $form->hidden('fields', array('value' => serialize($offers_export_fields)));
		echo $form->submit('CSV');
		echo $form->end();
		
		if (empty($business_session['Offer'])) {
		?>
		<p><em>K tomuto jednání se nevztahují žádné nabídky.</em></p>
		<?php } else { ?>
		<table class="top_heading">
			<tr>
				<th>ID</th>
				<th>Datum vytvoření</th>
				<th>Obsah</th>
				<th>&nbsp;</th>
			</tr>
		<?php
			$odd = '';
			foreach ($business_session['Offer'] as $offer) {
				$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
		?>
			<tr<?php echo $odd?>>
				<td><?php echo $offer['id']?></td>
				<td><?php echo $offer['created']?></td>
				<td><?php echo (strlen($offer['content']) <= 50 ? $offer['content'] : substr($offer['content'], 0, 50) . '...')?></td>
				<td class="actions">
					<?php echo $html->link('Detail', array('controller' => 'offers', 'action' => 'view', $offer['id']))?>
					<?php echo $html->link('Upravit', array('controller' => 'offers', 'action' => 'edit', $offer['id']))?>
					<?php echo $html->link('Smazat', array('controller' => 'offers', 'action' => 'delete', $offer['id']), null, 'Opravdu chcete nabídku odstranit?')?>
				</td>
			</tr>
		<?php } ?>
		</table>
		<?php } ?>
		<?php echo $html->link('Přidat nabídku', array('controller' => 'offers', 'action' => 'add', 'business_session_id' => $business_session['BusinessSession']['id']))?>
	</div>
</div>