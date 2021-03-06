/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_CsStorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'mage/url',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/calendar',
    'mage/mage',
], function ($, ko, Component, quote, shippingService, url, modal, t, calendar) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Ced_CsStorePickup/checkout/shipping/storepickup'
        },

        initialize: function (config) {
        	
        		
            this.selectedStores = ko.observable();
            this._super();  
        },

        initObservable: function () {
            this._super();
            this.selectedMethod = ko.computed(function() {
                var method = quote.shippingMethod();
                
            	if($('#onepage-checkout-shipping-method-additional-load')){
            		$('#onepage-checkout-shipping-method-additional-load').remove();
            	}
                var selectedMethod = method != null ? method.carrier_code + '_' + method.method_code : null;
                return selectedMethod;
            }, this);

            
            return this;
        },
    
        getStoreOffDays: function() {
            var storeId = $('#carrier-store-lists').val();
            console.log(storeId);
            var newUrl = url.build('csstorepickup/getmap/view/storeId/') + storeId;
             $.ajax({
                            method: 'GET',
                            dataType: 'html',
                            url: newUrl,
                        }).success(function (result) {
                            if($('#oneValues').length){
                                $('#oneValues').empty();
                            }
                            $('#oneValues').append(result);
                            
                        });
                        
            $('#div-calendar').show();
            var storevalue = $('#carrier-store-lists').val();
            return window.checkoutConfig.shipping.days[storevalue];
        },

        reloadStores: function() {
            storeService.getStoreList(quote.shippingAddress(), this);
        },

        getStores: function() {
            return window.checkoutConfig.shipping.storepickup.storelist;
        },

        showMap: function(){ 

        var mapurl = url.build('storepick/getmap/showmap/');
        var overlay = $('<div id="overlay" style=" position: fixed; top: 0; left: 0; width: 100%; height: 100%;background-color: #000; filter:alpha(opacity=50); -moz-opacity:0.5;-khtml-opacity: 0.5;opacity: 0.5; z-index: 10000; display: block;"> </div>');
        overlay.appendTo(document.body);
        $.ajax({
                    method: 'GET',
                    dataType: 'html',
                    url: mapurl,
                }).success(function (result) {
                      overlay.remove();
                        if ($('#new').length) {
                            $('#new').remove();
                            } 
                        var newDivThingy = document.createElement("div");
                          newDivThingy.id  = 'new'; 
                          document.getElementById('contentarea').appendChild(newDivThingy);
                          $('#new').append(result);
                          var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    title: 'Map View'
                };
                var popup = modal(options, $('#new'));
                    $('#new').modal('openModal');
                });  
            },
            storeValue: function(){
               var storeId = $('#carrier-store-lists').val();
               return storeId; 
            }        
    });     
});
