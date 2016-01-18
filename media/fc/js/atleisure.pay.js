jQuery.noConflict();
jQuery(document).ready(function ($) {
  $('#fullamount').click(function () {
    $('#downpayment').removeClass('selected');
    $(this).addClass('selected');
    $('#restWarning').hide();
    if (typeof jsonstring.payment2 == 'undefined') {
      payment1or2 = 'payment1'
    } else {
      payment1or2 = 'payment2'
    }
    if ($('#paypal').hasClass('selected')) {
      $('.costMessage').empty().append(jsonstring[payment1or2]['PayPal']['PayPal'].costsstring);
      $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + jsonstring[payment1or2].PayPal.PayPal.method + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2].PayPal.PayPal.costs + '</span></div></div>');
      $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2]['PayPal']['PayPal'].orgtotpluscosts);
      $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2]['PayPal']['PayPal'].amountpluscosts)
    }
    if ($('#creditcard').hasClass('selected')) {
      if ($('#creditcardselector').val() === '') {
        $('.costMessageCC').empty().append(jsonstring[payment1or2]['creditcard']['VISA'].costsstring);
        $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + woordcreditcard + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2]['creditcard']['VISA'].costs + '</span></div></div>');
        $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2]['creditcard']['VISA'].orgtotpluscosts);
        $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2]['creditcard']['VISA'].amountpluscosts)
      }
      if ($('#creditcardselector').val() !== '') {
        if ($('#creditcardselector').val() === 'A') {
          subselect = 'American Express'
        }
        if ($('#creditcardselector').val() === 'M') {
          subselect = 'Maestro'
        }
        if ($('#creditcardselector').val() === 'E') {
          subselect = 'Mastercard'
        }
        if ($('#creditcardselector').val() === 'V') {
          subselect = 'VISA'
        }
        $('.costMessageCC').empty().append(jsonstring[payment1or2]['creditcard'][subselect].costsstring);
        $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + jsonstring[payment1or2]['creditcard'][subselect].method + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2]['creditcard'][subselect].costs + '</span></div></div>');
        $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2]['creditcard'][subselect].orgtotpluscosts);
        $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2]['creditcard'][subselect].amountpluscosts)
      }
    }
  });
  
  $('#downpayment').click(function () {
    $('#fullamount').removeClass('selected');
    $(this).addClass('selected');
    $('#restWarning').show();
    payment1or2 = 'payment1';
    if ($('#paypal').hasClass('selected')) {
      $('.costMessage').empty().append(jsonstring[payment1or2]['PayPal']['PayPal'].costsstring);
      $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + jsonstring[payment1or2].PayPal.PayPal.method + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2].PayPal.PayPal.costs + '</span></div></div>');
      $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2]['PayPal']['PayPal'].orgtotpluscosts);
      $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2]['PayPal']['PayPal'].amountpluscosts)
    }
    if ($('#creditcard').hasClass('selected')) {
      if ($('#creditcardselector').val() === '') {
        $('.costMessageCC').empty().append(jsonstring[payment1or2]['creditcard']['VISA'].costsstring);
        $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + woordcreditcard + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2]['creditcard']['VISA'].costs + '</span></div></div>');
        $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2]['creditcard']['VISA'].orgtotpluscosts);
        $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2]['creditcard']['VISA'].amountpluscosts)
      }
      if ($('#creditcardselector').val() !== '') {
        if ($('#creditcardselector').val() === 'A') {
          subselect = 'American Express'
        }
        if ($('#creditcardselector').val() === 'M') {
          subselect = 'Maestro'
        }
        if ($('#creditcardselector').val() === 'E') {
          subselect = 'Mastercard'
        }
        if ($('#creditcardselector').val() === 'V') {
          subselect = 'VISA'
        }
        $('.costMessageCC').empty().append(jsonstring[payment1or2]['creditcard'][subselect].costsstring);
        $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + jsonstring[payment1or2]['creditcard'][subselect].method + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2]['creditcard'][subselect].costs + '</span></div></div>');
        $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2]['creditcard'][subselect].orgtotpluscosts);
        $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2]['creditcard'][subselect].amountpluscosts)
      }
    }
  });
  $('#ideal').click(function () {
    $(this).addClass('selected');
    $('#paypal, #creditcard, #banktransfer, #mrcash, #elv, #cheque').removeClass('selected');
    $('#costsWarning').hide();
    $('#selectBank').show();
    $('#selectCreditCard').hide();
    $('#selectCheque').hide();
    $('#payment-button').data('mainpaymentmethod', 'ideal');
    $('#payment-button').data('subpaymentmethod', '');
    $('#select-method').children().css('border-color', '#c8c8c8');
    $('#select-method').children().children().css('color', 'black');
    $('#nomainpaymentmethodwarning').hide();
    $('.subpayment').find('.btn-default').css('border-color', '#ccc');
    $('.nosubpaymentwarning').hide();
    payment1or2 = '';
    if ($('#fullamount').hasClass('selected')) {
      if (typeof jsonstring.payment2 == 'undefined') {
        payment1or2 = 'payment1'
      } else {
        payment1or2 = 'payment2'
      }
    }
    if ($('#downpayment').hasClass('selected')) {
      payment1or2 = 'payment1'
    }
    $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2].iDEAL['iDEAL / ABN AMRO'].orgtot);
    $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2].iDEAL['iDEAL / ABN AMRO'].amount);
    $('.extrapaymentcost').hide();
    if ($('#bankselector').val() !== '') {
      if ($('#bankselector').val() === 'IABNANL2A') {
        subselect = 'iDEAL / ABN AMRO'
      }
      if ($('#bankselector').val() === 'IASNBNL21') {
        subselect = 'iDEAL / ASN'
      }
      if ($('#bankselector').val() === 'IINGBNL2A') {
        subselect = 'iDEAL / ING Bank'
      }
      if ($('#bankselector').val() === 'IKNABNL2H') {
        subselect = 'iDEAL / KNAB Bank'
      }
      if ($('#bankselector').val() === 'IRABONL2U') {
        subselect = 'iDEAL / Rabobank'
      }
      if ($('#bankselector').val() === 'IRBRBNL21') {
        subselect = 'iDEAL / Regiobank'
      }
      if ($('#bankselector').val() === 'ISNSBNL2A') {
        subselect = 'iDEAL / SNS Bank'
      }
      if ($('#bankselector').val() === 'ITRIONL2U') {
        subselect = 'iDEAL / Triodos Bank'
      }
      if ($('#bankselector').val() === 'IFVLBNL22') {
        subselect = 'iDEAL / van Lanschotbank'
      }
      $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2].iDEAL['iDEAL / ABN AMRO'].orgtot);
      $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2].iDEAL['iDEAL / ABN AMRO'].amount);
      $('.extrapaymentcost').hide();
      if (jsonstring[payment1or2].iDEAL[subselect].costs !== '0,00') {
        $('#costsWarning-ideal').show();
        $('.costMessageIDEAL').empty().append(jsonstring[payment1or2].iDEAL[subselect].costsstring);
        $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + jsonstring[payment1or2].iDEAL[subselect].method + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2].iDEAL[subselect].costs + '</span></div></div>').show();
        $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2].iDEAL[subselect].orgtotpluscosts);
        $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2].iDEAL[subselect].amountpluscosts)
      }
    }
  });
  $('#paypal').click(function () {
    $(this).addClass('selected');
    $('#ideal, #creditcard, #banktransfer, #mrcash, #elv, #cheque').removeClass('selected');
    $('#costsWarning').hide();
    $('#selectBank').hide();
    $('#selectCreditCard').hide();
    $('#selectCheque').hide();
    $('#payment-button').data('mainpaymentmethod', 'paypal');
    $('#payment-button').data('subpaymentmethod', 'P');
    $('#select-method').children().css('border-color', '#c8c8c8');
    $('#select-method').children().children().css('color', 'black');
    $('#nomainpaymentmethodwarning').hide();
    $('.subpayment').find('.btn-default').css('border-color', '#ccc');
    $('.nosubpaymentwarning').hide();
    payment1or2 = '';
    if ($('#fullamount').hasClass('selected')) {
      if (typeof jsonstring.payment2 == 'undefined') {
        payment1or2 = 'payment1'
      } else {
        payment1or2 = 'payment2'
      }
    }
    if ($('#downpayment').hasClass('selected')) {
      payment1or2 = 'payment1'
    }
    $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2]['PayPal']['PayPal'].orgtot);
    $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2]['PayPal']['PayPal'].amount);
    $('.extrapaymentcost').hide();
    if (jsonstring[payment1or2]['PayPal']['PayPal'].costs !== '0,00') {
      $('#costsWarning').show();
      $('.costMessage').empty().append(jsonstring[payment1or2]['PayPal']['PayPal'].costsstring);
      $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + jsonstring[payment1or2]['PayPal']['PayPal'].method + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2]['PayPal']['PayPal'].costs + '</span></div></div>').show();
      $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2]['PayPal']['PayPal'].orgtotpluscosts);
      $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2]['PayPal']['PayPal'].amountpluscosts)
    }
  });
  $('#creditcard').click(function () {
    $(this).addClass('selected');
    $('#ideal, #paypal, #banktransfer, #mrcash, #elv, #cheque').removeClass('selected');
    $('#costsWarning').hide();
    $('#selectBank').hide();
    $('#selectCreditCard').show();
    $('#selectCheque').hide();
    $('#payment-button').data('mainpaymentmethod', 'creditcard');
    $('#payment-button').data('subpaymentmethod', '');
    $('#select-method').children().css('border-color', '#c8c8c8');
    $('#select-method').children().children().css('color', 'black');
    $('#nomainpaymentmethodwarning').hide();
    $('.subpayment').find('.btn-default').css('border-color', '#ccc');
    $('.nosubpaymentwarning').hide();
    payment1or2 = '';
    if ($('#fullamount').hasClass('selected')) {
      if (typeof jsonstring.payment2 == 'undefined') {
        payment1or2 = 'payment1'
      } else {
        payment1or2 = 'payment2'
      }
    }
    if ($('#downpayment').hasClass('selected')) {
      payment1or2 = 'payment1'
    }
    if (jsonstring[payment1or2].creditcard.VISA.costs !== '0,00') {
      $('#costsWarning-creditcard').show();
      $('.costMessageCC').empty().append(jsonstring[payment1or2].creditcard.VISA.costsstring);
      $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + woordcreditcard + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2].creditcard.VISA.costs + '</span></div></div>').show();
      $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2].creditcard.VISA.orgtotpluscosts);
      $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2].creditcard.VISA.amountpluscosts)
    }
    if ($('#creditcardselector').val() !== '') {
      if ($('#creditcardselector').val() === 'A') {
        subselect = 'American Express'
      }
      if ($('#creditcardselector').val() === 'M') {
        subselect = 'Maestro'
      }
      if ($('#creditcardselector').val() === 'E') {
        subselect = 'Mastercard'
      }
      if ($('#creditcardselector').val() === 'V') {
        subselect = 'VISA'
      }
      $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2]['creditcard']['VISA'].orgtot);
      $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2]['creditcard']['VISA'].amount);
      $('.extrapaymentcost').hide();
      if (jsonstring[payment1or2].creditcard[subselect].costs !== '0,00') {
        $('#costsWarning-creditcard').show();
        $('.costMessageCC').empty().append(jsonstring[payment1or2].creditcard[subselect].costsstring);
        $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + jsonstring[payment1or2].creditcard[subselect].method + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2].creditcard[subselect].costs + '</span></div></div>').show();
        $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2].creditcard[subselect].orgtotpluscosts);
        $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2].creditcard[subselect].amountpluscosts)
      }
    }
  });
  $('#banktransfer').click(function () {
    $(this).addClass('selected');
    $('#ideal, #paypal, #creditcard, #mrcash, #elv, #cheque').removeClass('selected');
    $('#costsWarning').hide();
    $('#selectBank').hide();
    $('#selectCreditCard').hide();
    $('#selectCheque').hide();
    $('#payment-button').data('mainpaymentmethod', 'banktransfer');
    $('#payment-button').data('subpaymentmethod', 'bank');
    $('#select-method').children().css('border-color', '#c8c8c8');
    $('#select-method').children().children().css('color', 'black');
    $('#nomainpaymentmethodwarning').hide();
    $('.subpayment').find('.btn-default').css('border-color', '#ccc');
    $('.nosubpaymentwarning').hide()
  });
  $('#mrcash').click(function () {
    $(this).addClass('selected');
    $('#ideal, #paypal, #creditcard, #banktransfer, #elv, #cheque').removeClass('selected');
    $('#costsWarning').hide();
    $('#selectBank').hide();
    $('#selectCreditCard').hide();
    $('#selectCheque').hide();
    $('#payment-button').data('mainpaymentmethod', 'mrcash');
    $('#payment-button').data('subpaymentmethod', 'C');
    $('#select-method').children().css('border-color', '#c8c8c8');
    $('#select-method').children().children().css('color', 'black');
    $('#nomainpaymentmethodwarning').hide();
    $('.subpayment').find('.btn-default').css('border-color', '#ccc');
    $('.nosubpaymentwarning').hide();
    payment1or2 = '';
    if ($('#fullamount').hasClass('selected')) {
      if (typeof jsonstring.payment2 == 'undefined') {
        payment1or2 = 'payment1'
      } else {
        payment1or2 = 'payment2'
      }
    }
    if ($('#downpayment').hasClass('selected')) {
      payment1or2 = 'payment1'
    }
    $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2]['Bancontact / Mister Cash']['Bancontact / Mister Cash'].orgtot);
    $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2]['Bancontact / Mister Cash']['Bancontact / Mister Cash'].amount);
    $('.extrapaymentcost').hide();
    if (jsonstring[payment1or2]['Bancontact / Mister Cash']['Bancontact / Mister Cash'].costs !== '0,00') {
      $('#costsWarning').show();
      $('.costMessage').empty().append(jsonstring[payment1or2]['Bancontact / Mister Cash']['Bancontact / Mister Cash'].costsstring);
      $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + jsonstring[payment1or2]['Bancontact / Mister Cash']['Bancontact / Mister Cash'].method + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2]['Bancontact / Mister Cash']['Bancontact / Mister Cash'].costs + '</span></div></div>').show();
      $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2]['Bancontact / Mister Cash']['Bancontact / Mister Cash'].orgtotpluscosts);
      $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2]['Bancontact / Mister Cash']['Bancontact / Mister Cash'].amountpluscosts)
    }
  });
  $('#elv').click(function () {
    $(this).addClass('selected');
    $('#ideal, #paypal, #creditcard, #banktransfer, #mrcash, #cheque').removeClass('selected');
    $('#costsWarning').hide();
    $('#selectBank').hide();
    $('#selectCreditCard').hide();
    $('#selectCheque').hide();
    $('#payment-button').data('mainpaymentmethod', 'elv');
    $('#payment-button').data('subpaymentmethod', 'L');
    $('#select-method').children().css('border-color', '#c8c8c8');
    $('#select-method').children().children().css('color', 'black');
    $('#nomainpaymentmethodwarning').hide();
    $('.subpayment').find('.btn-default').css('border-color', '#ccc');
    $('.nosubpaymentwarning').hide();
    payment1or2 = '';
    if ($('#fullamount').hasClass('selected')) {
      if (typeof jsonstring.payment2 == 'undefined') {
        payment1or2 = 'payment1'
      } else {
        payment1or2 = 'payment2'
      }
    }
    if ($('#downpayment').hasClass('selected')) {
      payment1or2 = 'payment1'
    }
    $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2]['Einmaliges Lastschriftverfahren']['Einmaliges Lastschriftverfahren'].orgtot);
    $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2]['Einmaliges Lastschriftverfahren']['Einmaliges Lastschriftverfahren'].amount);
    $('.extrapaymentcost').hide();
    if (jsonstring[payment1or2]['Einmaliges Lastschriftverfahren']['Einmaliges Lastschriftverfahren'].costs !== '0,00') {
      $('#costsWarning').show();
      $('.costMessage').empty().append(jsonstring[payment1or2]['Einmaliges Lastschriftverfahren']['Einmaliges Lastschriftverfahren'].costsstring);
      $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + jsonstring[payment1or2]['Einmaliges Lastschriftverfahren']['Einmaliges Lastschriftverfahren'].method + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2]['Einmaliges Lastschriftverfahren']['Einmaliges Lastschriftverfahren'].costs + '</span></div></div>').show();
      $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2]['Einmaliges Lastschriftverfahren']['Einmaliges Lastschriftverfahren'].orgtotpluscosts);
      $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2]['Einmaliges Lastschriftverfahren']['Einmaliges Lastschriftverfahren'].amountpluscosts)
    }
  });
  $('#cheque').click(function () {
    $(this).addClass('selected');
    $('#ideal, #paypal, #creditcard, #banktransfer, #mrcash, #elv').removeClass('selected');
    $('#costsWarning').hide();
    $('#selectBank').hide();
    $('#selectCreditCard').hide();
    $('#selectCheque').show();
    $('#payment-button').data('mainpaymentmethod', 'cheque');
    $('#payment-button').data('subpaymentmethod', '');
    $('#select-method').children().css('border-color', '#c8c8c8');
    $('#select-method').children().children().css('color', 'black');
    $('#nomainpaymentmethodwarning').hide();
    $('.subpayment').find('.btn-default').css('border-color', '#ccc');
    $('.nosubpaymentwarning').hide();
    if ($('#chequeselector').val() !== '') {
      if ($('#chequeselector').val() === 'F') {
        subselect = 'Cheque de banque'
      }
      if ($('#chequeselector').val() === 'N') {
        subselect = 'Cheque de vacances'
      }
      $('.totbedr').empty().append('&nbsp;' + jsonstring.payment1.creditcard['VISA'].orgtot);
      $('.nogbet').empty().append('&nbsp;' + jsonstring.payment1.creditcard['VISA'].amount);
      $('.extrapaymentcost').hide();
      if (jsonstring[payment1or2].cheque[subselect].costs !== '0,00') {
        $('#costsWarning-cheque').show();
        $('.costMessageCH').empty().append(jsonstring[payment1or2].cheque[subselect].costsstring);
        $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + jsonstring[payment1or2].cheque[subselect].method + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2].cheque[subselect].costs + '</span></div></div>').show();
        $('.totbedr').empty().append('&nbsp;' + jsonstring.payment1.creditcard['VISA'].orgtotpluscosts);
        $('.nogbet').empty().append('&nbsp;' + jsonstring.payment1.creditcard['VISA'].amountpluscosts)
      }
    }
  });
  $('#bankselector').change(function () {
    $('.subpayment').find('.btn-default').css('border-color', '#ccc');
    $('.nosubpaymentwarning').hide();
    $('#payment-button').data('subpaymentmethod', $(this).val());
    payment1or2 = '';
    subselect = '';
    if ($('#fullamount').hasClass('selected')) {
      if (typeof jsonstring.payment2 == 'undefined') {
        payment1or2 = 'payment1'
      } else {
        payment1or2 = 'payment2'
      }
    }
    if ($('#downpayment').hasClass('selected')) {
      payment1or2 = 'payment1'
    }
    if ($(this).val() === 'IABNANL2A') {
      subselect = 'iDEAL / ABN AMRO'
    }
    if ($(this).val() === 'IASNBNL21') {
      subselect = 'iDEAL / ASN'
    }
    if ($(this).val() === 'IINGBNL2A') {
      subselect = 'iDEAL / ING Bank'
    }
    if ($(this).val() === 'IKNABNL2H') {
      subselect = 'iDEAL / KNAB Bank'
    }
    if ($(this).val() === 'IRABONL2U') {
      subselect = 'iDEAL / Rabobank'
    }
    if ($(this).val() === 'IRBRBNL21') {
      subselect = 'iDEAL / Regiobank'
    }
    if ($(this).val() === 'ISNSBNL2A') {
      subselect = 'iDEAL / SNS Bank'
    }
    if ($(this).val() === 'ITRIONL2U') {
      subselect = 'iDEAL / Triodos Bank'
    }
    if ($(this).val() === 'IFVLBNL22') {
      subselect = 'iDEAL / van Lanschotbank'
    }
    $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2].iDEAL['iDEAL / ABN AMRO'].orgtot);
    $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2].iDEAL['iDEAL / ABN AMRO'].amount);
    $('.extrapaymentcost').hide();
    if (jsonstring[payment1or2].iDEAL[subselect].costs !== '0,00') {
      $('#costsWarning-ideal').show();
      $('.costMessageIDEAL').empty().append(jsonstring[payment1or2].iDEAL[subselect].costsstring);
      $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + jsonstring[payment1or2].iDEAL[subselect].method + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2].iDEAL[subselect].costs + '</span></div></div>').show();
      $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2].iDEAL[subselect].orgtotpluscosts);
      $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2].iDEAL[subselect].amountpluscosts)
    }
  });
  $('#creditcardselector').change(function () {
    $('.subpayment').find('.btn-default').css('border-color', '#ccc');
    $('.nosubpaymentwarning').hide();
    $('#payment-button').data('subpaymentmethod', $(this).val());
    payment1or2 = '';
    subselect = '';
    if ($('#fullamount').hasClass('selected')) {
      if (typeof jsonstring.payment2 == 'undefined') {
        payment1or2 = 'payment1'
      } else {
        payment1or2 = 'payment2'
      }
    }
    if ($('#downpayment').hasClass('selected')) {
      payment1or2 = 'payment1'
    }
    if ($(this).val() === 'A') {
      subselect = 'American Express'
    }
    if ($(this).val() === 'M') {
      subselect = 'Maestro'
    }
    if ($(this).val() === 'E') {
      subselect = 'Mastercard'
    }
    if ($(this).val() === 'V') {
      subselect = 'VISA'
    }
    $('.totbedr').empty().append('&nbsp;' + jsonstring.payment1.creditcard['VISA'].orgtot);
    $('.nogbet').empty().append('&nbsp;' + jsonstring.payment1.creditcard['VISA'].amount);
    $('.extrapaymentcost').hide();
    if (jsonstring[payment1or2].creditcard[subselect].costs !== '0,00') {
      $('#costsWarning-creditcard').show();
      $('.costMessageCC').empty().append(jsonstring[payment1or2].creditcard[subselect].costsstring);
      $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + jsonstring[payment1or2].creditcard[subselect].method + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2].creditcard[subselect].costs + '</span></div></div>').show();
      $('.totbedr').empty().append('&nbsp;' + jsonstring[payment1or2].creditcard[subselect].orgtotpluscosts);
      $('.nogbet').empty().append('&nbsp;' + jsonstring[payment1or2].creditcard[subselect].amountpluscosts)
    }
  });
  $('#chequeselector').change(function () {
    $('.subpayment').find('.btn-default').css('border-color', '#ccc');
    $('.nosubpaymentwarning').hide();
    $('#payment-button').data('subpaymentmethod', $(this).val());
    payment1or2 = '';
    subselect = '';
    if ($('#fullamount').hasClass('selected')) {
      if (typeof jsonstring.payment2 == 'undefined') {
        payment1or2 = 'payment1'
      } else {
        payment1or2 = 'payment2'
      }
    }
    if ($('#downpayment').hasClass('selected')) {
      payment1or2 = 'payment1'
    }
    if ($(this).val() === 'F') {
      subselect = 'Cheque de banque'
    }
    if ($(this).val() === 'N') {
      subselect = 'Cheque de vacances'
    }
    $('.totbedr').empty().append('&nbsp;' + jsonstring.payment1.creditcard['VISA'].orgtot);
    $('.nogbet').empty().append('&nbsp;' + jsonstring.payment1.creditcard['VISA'].amount);
    $('.extrapaymentcost').hide();
    if (jsonstring[payment1or2].cheque[subselect].costs !== '0,00') {
      $('#costsWarning-cheque').show();
      $('.costMessageCH').empty().append(jsonstring[payment1or2].cheque[subselect].costsstring);
      $('.extrapaymentcost').empty().append('<div class="cost row"><div class="description col-sm-8" style="font-weight:bold;">' + jsonstring[payment1or2].cheque[subselect].method + '</div><div class="value col-sm-4"><span class="currency">&euro;</span><span class="amount">&nbsp;' + jsonstring[payment1or2].cheque[subselect].costs + '</span></div></div>').show();
      $('.totbedr').empty().append('&nbsp;' + jsonstring.payment1.creditcard['VISA'].orgtotpluscosts);
      $('.nogbet').empty().append('&nbsp;' + jsonstring.payment1.creditcard['VISA'].amountpluscosts)
    }
  });
  $('#payment-button').click(function () {
    payment1or2 = '';
    methodselect = '';
    if ($('#fullamount').hasClass('selected')) {
      if (typeof jsonstring.payment2 == 'undefined') {
        payment1or2 = 'payment1'
      } else {
        payment1or2 = 'payment2'
      }
    }
    if ($('#downpayment').hasClass('selected')) {
      payment1or2 = 'payment1'
    }
    mainpaymentmethod = $(this).data('mainpaymentmethod');
    subpaymentmethod = $(this).data('subpaymentmethod');
    if (typeof mainpaymentmethod === 'undefined' || mainpaymentmethod == '') {
      $('#select-method').children().css('border-color', '#D44343');
      $('#select-method').children().children().css('color', '#D44343');
      $('#nomainpaymentmethodwarning').show()
    }
    if (typeof subpaymentmethod === 'undefined' || subpaymentmethod == '') {
      $('.subpayment').find('.btn-default').css('border-color', '#D44343');
      $('.nosubpaymentwarning').show()
    }
    if (mainpaymentmethod === 'paypal') {
      methodselect = 'PayPal';
      subselect = 'PayPal'
    }
    if (mainpaymentmethod === 'ideal') {
      methodselect = 'iDEAL';
      if (subpaymentmethod === 'IABNANL2A') {
        subselect = 'iDEAL / ABN AMRO'
      }
      if (subpaymentmethod === 'IASNBNL21') {
        subselect = 'iDEAL / ASN'
      }
      if (subpaymentmethod === 'IINGBNL2A') {
        subselect = 'iDEAL / ING Bank'
      }
      if (subpaymentmethod === 'IKNABNL2H') {
        subselect = 'iDEAL / KNAB Bank'
      }
      if (subpaymentmethod === 'IRABONL2U') {
        subselect = 'iDEAL / Rabobank'
      }
      if (subpaymentmethod === 'IRBRBNL21') {
        subselect = 'iDEAL / Regiobank'
      }
      if (subpaymentmethod === 'ISNSBNL2A') {
        subselect = 'iDEAL / SNS Bank'
      }
      if (subpaymentmethod === 'ITRIONL2U') {
        subselect = 'iDEAL / Triodos Bank'
      }
      if (subpaymentmethod === 'IFVLBNL22') {
        subselect = 'iDEAL / van Lanschotbank'
      }
    }
    if (mainpaymentmethod === 'creditcard') {
      methodselect = 'creditcard';
      if (subpaymentmethod === 'A') {
        subselect = 'American Express'
      }
      if (subpaymentmethod === 'M') {
        subselect = 'Maestro'
      }
      if (subpaymentmethod === 'E') {
        subselect = 'Mastercard'
      }
      if (subpaymentmethod === 'V') {
        subselect = 'VISA'
      }
    }
    if (mainpaymentmethod === 'mrcash') {
      methodselect = 'Bancontact / Mister Cash';
      subselect = 'Bancontact / Mister Cash'
    }
    if (mainpaymentmethod === 'cheque') {
      methodselect = 'cheque';
      if (subpaymentmethod === 'F') {
        subselect = 'Cheque de banque'
      }
      if (subpaymentmethod === 'N') {
        subselect = 'Cheque de vacances'
      }
    }
    if (mainpaymentmethod === 'elv') {
      methodselect = 'Einmaliges Lastschriftverfahren';
      subselect = 'Einmaliges Lastschriftverfahren'
    }
    if (typeof subselect !== 'undefined') {
      $(this).attr('href', jsonstring[payment1or2][methodselect][subselect].URL);
      $.post('/cgi/lars/srv/customerpayment/set_olb-time.p', {
        huovid: huovid,
        huovnr: huovnr
      }, function (a) {
        if (a.status == 'OK') {
        } else {
          console.log(a.message)
        }
      }, 'json')
    }
  });
});
