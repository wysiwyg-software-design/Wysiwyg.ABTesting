(()=>{var i={abTestingCookieString:"",cookieName:document.body.dataset.abTestingCookieName||"WYSIWYG_AB_TESTING",abTestingObject:{},init:function(){this.abTestingCookieString=this.getCookie(),this.abTestingObject=JSON.parse(this.abTestingCookieString||"{}")},getCookie:function(){let t=document.cookie.match(new RegExp("(^| )"+this.cookieName+"=([^;]+)"));if(t)return decodeURIComponent(t[2])},getTrackingStringsArrayForAllFeatures:function(){let t=[];for(let e in this.abTestingObject)t.push(this.getTrackingStringForFeature(e));return t},getTrackingStringForFeature:function(t){return t+"_"+this.getDecisionForFeature(t)},getDecisionsForAllFeatures:function(){return this.abTestingObject},getDecisionForFeature:function(t){if(t in this.abTestingObject)return this.abTestingObject[t]}};window.WY=window.WY||{};window.WY.AbTesting=i;window.addEventListener("load",function(){window.WY.AbTesting.init()});})();
