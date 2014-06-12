<?
	echo 'CISLO_OBJEDNAVKY;NAZEV_PRODUKTU;DATUM_VYTVORENI;CAS_VYTVORENI;MNOZSTVI_PRODUKTU;CENA_KS_BEZ_DPH;CENA_KS_S_DPH;CELKOVA_CENA_S_DPH;NAZEV_STAVU_OBJEDNAVKY;PSC_FAKTURACNI;PSC_DORUCOVACI;ID_ZAKAZNIKA;JMENO_ZAKAZNIKA' . "\n";
	foreach ( $orders as $order ){
		foreach ( $order['OrderedProduct'] as $op ){
			$tax = 1 + intval($op['Product']['TaxClass']['value']) / 100;
			$wout_tax = round($op['product_price_with_dph'] / $tax, 2);
			$total = round($op['product_price_with_dph'] * $op['product_quantity'], 2);
			$wout_tax = str_replace('.', ',', $wout_tax);
			$op['product_price_with_dph'] = str_replace('.', ',', $op['product_price_with_dph']);
			$total = str_replace('.', ',', $total);
			
			$date = explode(" ", $op['created']);
			$line = $op['order_id'] . ';"' . $op['Product']['name'] . '";' . $date[0] . ';' . $date[1] . ';' . $op['product_quantity'] . ';' . $wout_tax . ';' . $op['product_price_with_dph'] . ';' . $total . ';' . $order['Status']['name'] . ';' . $order['Order']['customer_zip'] . ';' . $order['Order']['delivery_zip'] . ';' . $order['Order']['customer_id'] . ';' . $order['Order']['customer_name'] . "\n";
			$line = iconv('utf-8', 'windows-1250', $line);
			echo $line;
		}
	}
?>