
var payolutionXDate=function(g,m,A,p){function f(){var a=this instanceof f?this:new f,c=arguments,b=c.length,d;typeof c[b-1]=="boolean"&&(d=c[--b],c=q(c,0,b));if(b)if(b==1)if(b=c[0],b instanceof g||typeof b=="number")a[0]=new g(+b);else if(b instanceof f){var c=a,h=new g(+b[0]);if(l(b))h.toString=w;c[0]=h}else{if(typeof b=="string"){a[0]=new g(0);a:{for(var c=b,b=d||!1,h=f.parsers,r=0,e;r<h.length;r++)if(e=h[r](c,b,a)){a=e;break a}a[0]=new g(c)}}}else a[0]=new g(n.apply(g,c)),d||(a[0]=s(a[0]));else a[0]=new g;typeof d=="boolean"&&B(a,d);return a}function l(a){return a[0].toString===w}function B(a,c,b){if(c){if(!l(a))b&&(a[0]=new g(n(a[0].getFullYear(),a[0].getMonth(),a[0].getDate(),a[0].getHours(),a[0].getMinutes(),a[0].getSeconds(),a[0].getMilliseconds()))),a[0].toString=w}else l(a)&&(a[0]=b?s(a[0]):new g(+a[0]));return a}function C(a,c,b,d,h){var e=k(j,a[0],h),a=k(D,a[0],h),h=c==1?b%12:e(1),f=!1;d.length==2&&typeof d[1]=="boolean"&&(f=d[1],d=[b]);a(c,d);f&&e(1)!=h&&(a(1,[e(1)-1]),a(2,[E(e(0),e(1))]))}
function F(a,c,b,d){var b=Number(b),h=m.floor(b);a["set"+o[c]](a["get"+o[c]]()+h,d||!1);h!=b&&c<6&&F(a,c+1,(b-h)*G[c],d)}function H(a,c,b){var a=a.clone().setUTCMode(!0,!0),c=f(c).setUTCMode(!0,!0),d=0;if(b==0||b==1){for(var h=6;h>=b;h--)d/=G[h],d+=j(c,!1,h)-j(a,!1,h);b==1&&(d+=(c.getFullYear()-a.getFullYear())*12)}else b==2?(b=a.toDate().setUTCHours(0,0,0,0),d=c.toDate().setUTCHours(0,0,0,0),d=m.round((d-b)/864E5)+(c-d-(a-b))/864E5):d=(c-a)/[36E5,6E4,1E3,1][b-3];return d}function t(a){var c=a(0),b=a(1),a=a(2),b=new g(n(c,b,a)),d=u(c),a=d;b<d?a=u(c-1):(c=u(c+1),b>=c&&(a=c));return m.floor(m.round((b-a)/864E5)/7)+1}function u(a){a=new g(n(a,0,4));a.setUTCDate(a.getUTCDate()-(a.getUTCDay()+6)%7);return a}function I(a,c,b,d){var h=k(j,a,d),e=k(D,a,d),b=u(b===p?h(0):b);d||(b=s(b));a.setTime(+b);e(2,[h(2)+(c-1)*7])}function J(a,c,b,d,e){var r=f.locales,g=r[f.defaultLocale]||{},i=k(j,a,e),b=(typeof b=="string"?r[b]:b)||{};return x(a,c,function(a){if(d)for(var b=(a==7?2:a)-1;b>=0;b--)d.push(i(b));return i(a)},function(a){return b[a]||g[a]},e)}function x(a,c,b,d,e){for(var f,g,i="";f=c.match(M);){i+=c.substr(0,f.index);if(f[1]){g=i;for(var i=a,j=f[1],l=b,m=d,n=e,k=j.length,o=void 0,q="";k>0;)o=N(i,j.substr(0,k),l,m,n),o!==p?(q+=o,j=j.substr(k),k=j.length):k--;i=g+(q+j)}else f[3]?(g=x(a,f[4],b,d,e),parseInt(g.replace(/\D/g,""),10)&&(i+=g)):i+=f[7]||"'";c=c.substr(f.index+f[0].length)}return i+c}function N(a,c,b,d,e){var g=f.formatters[c];if(typeof g=="string")return x(a,g,b,d,e);else if(typeof g=="function")return g(a,e||!1,d);switch(c){case"fff":return i(b(6),3);case"s":return b(5);case"ss":return i(b(5));case"m":return b(4);case"mm":return i(b(4));case"h":return b(3)%12||12;case"hh":return i(b(3)%12||12);case"H":return b(3);case"HH":return i(b(3));case"d":return b(2);case"dd":return i(b(2));case"ddd":return d("dayNamesShort")[b(7)]||"";case"dddd":return d("dayNames")[b(7)]||"";case"M":return b(1)+1;case"MM":return i(b(1)+1);case"MMM":return d("monthNamesShort")[b(1)]||"";case"MMMM":return d("monthNames")[b(1)]||"";case"yy":return(b(0)+"").substring(2);case"yyyy":return b(0);case"t":return v(b,d).substr(0,1).toLowerCase();case"tt":return v(b,d).toLowerCase();case"T":return v(b,d).substr(0,1);case"TT":return v(b,d);case"z":case"zz":case"zzz":return e?c="Z":(d=a.getTimezoneOffset(),a=d<0?"+":"-",b=m.floor(m.abs(d)/60),d=m.abs(d)%60,e=b,c=="zz"?e=i(b):c=="zzz"&&(e=i(b)+":"+i(d)),c=a+e),c;case"w":return t(b);case"ww":return i(t(b));case"S":return c=b(2),c>10&&c<20?"th":["st","nd","rd"][c%10-1]||"th"}}function v(a,c){return a(3)<12?c("amDesignator"):c("pmDesignator")}function y(a){return!isNaN(+a[0])}function j(a,c,b){return a["get"+(c?"UTC":"")+o[b]]()}function D(a,c,b,d){a["set"+(c?"UTC":"")+o[b]].apply(a,d)}function s(a){return new g(a.getUTCFullYear(),a.getUTCMonth(),a.getUTCDate(),a.getUTCHours(),a.getUTCMinutes(),a.getUTCSeconds(),a.getUTCMilliseconds())}function E(a,c){return 32-(new g(n(a,c,32))).getUTCDate()}function z(a){return function(){return a.apply(p,[this].concat(q(arguments)))}}function k(a){var c=q(arguments,1);return function(){return a.apply(p,c.concat(q(arguments)))}}function q(a,c,b){return A.prototype.slice.call(a,c||0,b===p?a.length:b)}function K(a,c){for(var b=0;b<a.length;b++)c(a[b],b)}function i(a,c){c=c||2;for(a+="";a.length<c;)a="0"+a;return a}var o="FullYear,Month,Date,Hours,Minutes,Seconds,Milliseconds,Day,Year".split(","),L=["Years","Months","Days"],G=[12,31,24,60,60,1E3,1],M=/(([a-zA-Z])\2*)|(\((('.*?'|\(.*?\)|.)*?)\))|('(.*?)')/,n=g.UTC,w=g.prototype.toUTCString,e=f.prototype;e.length=1;e.splice=A.prototype.splice;e.getUTCMode=z(l);e.setUTCMode=z(B);e.getTimezoneOffset=function(){return l(this)?0:this[0].getTimezoneOffset()};K(o,function(a,c){e["get"+a]=function(){return j(this[0],l(this),c)};c!=8&&(e["getUTC"+a]=function(){return j(this[0],!0,c)});c!=7&&(e["set"+a]=function(a){C(this,c,a,arguments,l(this));return this},c!=8&&(e["setUTC"+a]=function(a){C(this,c,a,arguments,!0);return this},e["add"+(L[c]||a)]=function(a,d){F(this,c,a,d);return this},e["diff"+(L[c]||a)]=function(a){return H(this,a,c)}))});e.getWeek=function(){return t(k(j,this,!1))};e.getUTCWeek=function(){return t(k(j,this,!0))};e.setWeek=function(a,c){I(this,a,c,!1);return this};e.setUTCWeek=function(a,c){I(this,a,c,!0);return this};e.addWeeks=function(a){return this.addDays(Number(a)*7)};e.diffWeeks=function(a){return H(this,a,2)/7};f.parsers=[function(a,c,b){if(a=a.match(/^(\d{4})(-(\d{2})(-(\d{2})([T ](\d{2}):(\d{2})(:(\d{2})(\.(\d+))?)?(Z|(([-+])(\d{2})(:?(\d{2}))?))?)?)?)?$/)){var d=new g(n(a[1],a[3]?a[3]-1:0,a[5]||1,a[7]||0,a[8]||0,a[10]||0,a[12]?Number("0."+a[12])*1E3:0));a[13]?a[14]&&d.setUTCMinutes(d.getUTCMinutes()+(a[15]=="-"?1:-1)*(Number(a[16])*60+(a[18]?Number(a[18]):0))):c||(d=s(d));return b.setTime(+d)}}];f.parse=function(a){return+f(""+a)};e.toString=function(a,c,b){return a===p||!y(this)?this[0].toString():J(this,a,c,b,l(this))};e.toUTCString=e.toGMTString=function(a,c,b){return a===p||!y(this)?this[0].toUTCString():J(this,a,c,b,!0)};e.toISOString=function(){return this.toUTCString("yyyy-MM-dd'T'HH:mm:ss(.fff)zzz")};f.defaultLocale="";f.locales={"":{monthNames:"January,February,March,April,May,June,July,August,September,October,November,December".split(","),monthNamesShort:"Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec".split(","),dayNames:"Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday".split(","),dayNamesShort:"Sun,Mon,Tue,Wed,Thu,Fri,Sat".split(","),amDesignator:"AM",pmDesignator:"PM"}};f.formatters={i:"yyyy-MM-dd'T'HH:mm:ss(.fff)",u:"yyyy-MM-dd'T'HH:mm:ss(.fff)zzz"};K("getTime,valueOf,toDateString,toTimeString,toLocaleString,toLocaleDateString,toLocaleTimeString,toJSON".split(","),function(a){e[a]=function(){return this[0][a]()}});e.setTime=function(a){this[0].setTime(a);return this};e.valid=z(y);e.clone=function(){return new f(this)};e.clearTime=function(){return this.setHours(0,0,0,0)};e.toDate=function(){return new g(+this[0])};f.now=function(){return+new g};f.today=function(){return(new f).clearTime()};f.UTC=n;f.getDaysInMonth=E;if(typeof module!=="undefined"&&module.exports)module.exports=f;return f}(Date,Math,Array);(function(global){var SCALE=10;var ACCURACY=1.0E-8;var LOWER_BOUND=1.0E-10;var UPPER_BOUND=1000000.0;var AVAILABLE_INTEREST_RATES=[6.6]
var PRICE_MATRIX={"6.6":[3,4,6,12,24]}
var DEFAULT_INTEREST_RATE="6.6"
var AVAILABLE_DURATIONS=[3,4,6,12,24];var DURATION_BASED_INTEREST_RATES={"3":6.6,"4":6.6,"6":6.6,"12":6.6,"24":6.6};var MINIMUM_AMOUNT=0;var MAXIMUM_AMOUNT=5000;var DEFAULT_FULFILLMENT_DATE=payolutionXDate.today();var FIRST_DUE_OFFSET=14;var INTEREST_FREE_DAYS=0;var INTEREST_STRATEGY="DEFAULT";var FULFILLMENT_BASE="FULFILLMENT_END";var CALCULATED_AFTERWARDS=0;var InstallmentCalculator=function(originalAmount,duration,interestRate,currency,referenceDay){this.originalAmount=originalAmount;this.currency=currency;this.duration=duration;this.accuracy=ACCURACY;this.lowerBound=LOWER_BOUND;this.upperBound=UPPER_BOUND;this.interestRate=interestRate;this.minimum_amount=MINIMUM_AMOUNT;this.maximum_amount=MAXIMUM_AMOUNT;this.finalAmount=CALCULATED_AFTERWARDS;this.effectiveInterest=CALCULATED_AFTERWARDS;this.installments=[];this.daysToInstallments=[];this.interestDaysBetweenInstallments=[];this.referenceDay=referenceDay;this.interestStartDay=this.referenceDay.clone().addDays(INTEREST_FREE_DAYS);this.firstDueOffset=FIRST_DUE_OFFSET;this.defaultDueDayOfMonth=CALCULATED_AFTERWARDS;this.firstDueDate=CALCULATED_AFTERWARDS;}
InstallmentCalculator.prototype.calculate=function(){this.calculateDueDates();var installmentAmount=this.calculateInstallmentAmount();this.updateInstallments(installmentAmount);this.calculateEffectiveInterest();this.calculateFinalAmount();return{installmentAmount:installmentAmount,interestRate:this.interestRate,effectiveInterest:this.effectiveInterest,totalAmount:this.finalAmount,installments:this.installments}}
InstallmentCalculator.prototype.calculateDueDates=function(){this.setFirstDueDate();for(var i=0;i<this.duration;i++){this.installments.push({amount:CALCULATED_AFTERWARDS,dueDate:this.getNextDue(i)});this.daysToInstallments.push(this.referenceDay.diffDays(new payolutionXDate(this.installments[i].dueDate)));if(i==0){this.interestDaysBetweenInstallments.push(Math.max(0,this.interestStartDay.diffDays(new payolutionXDate(this.installments[i].dueDate))));}else{var baseDate=new payolutionXDate(Math.max(new payolutionXDate(this.installments[i-1].dueDate),this.interestStartDay));this.interestDaysBetweenInstallments.push(Math.max(0,baseDate.diffDays(new payolutionXDate(this.installments[i].dueDate))));}}}
InstallmentCalculator.prototype.setFirstDueDate=function(){this.firstDueDate=this.referenceDay.clone().addDays(this.firstDueOffset);this.setDefaultDueDayOfMonth();if(this.firstDueDate.getDate()>this.defaultDueDayOfMonth){this.firstDueDate.addMonths(1,true);}
var monthDays=payolutionXDate.getDaysInMonth(this.firstDueDate.getFullYear(),this.firstDueDate.getMonth());this.firstDueDate.setDate(Math.min(this.defaultDueDayOfMonth,monthDays));}
InstallmentCalculator.prototype.setDefaultDueDayOfMonth=function(){var day=this.firstDueDate.getDate();if(day>5&&day<=20){this.defaultDueDayOfMonth=20;}else{this.defaultDueDayOfMonth=5;}}
InstallmentCalculator.prototype.getNextDue=function(monthOffset){if(monthOffset==0){return this.firstDueDate.clone().toDate();}
var nextDueDate=this.firstDueDate.clone().addMonths(monthOffset,true);return nextDueDate.toDate();}
InstallmentCalculator.prototype.calculateBinarySearch=function(lowerBound,upperBound,result,resultScale,getErrorFunction){var error=getErrorFunction(result,this);for(var i=0;i<1000&&((Math.abs(error)-this.accuracy)>0);i++){if(error>0){upperBound=result;}else{lowerBound=result;}
result=upperBound-((upperBound-lowerBound)*0.5);error=getErrorFunction(result,this);}
return+result.toFixed(resultScale);}
InstallmentCalculator.prototype.getInstallmentAmountError=function(result,calculator){var current=calculator.originalAmount;for(var i=0;i<calculator.duration;i++){var interest=calculator.getInterest(current,i);current=current+interest-result;}
return(-1)*current;}
InstallmentCalculator.prototype.getEffectiveInterestError=function(result,calculator){var sum=0;for(var i=0;i<calculator.duration;i++){var days=calculator.daysToInstallments[i];var power=Math.pow(1+result,days/365);var multiplicand=1/power;var current=calculator.installments[i].amount*multiplicand;sum=sum+current;}
return calculator.originalAmount-sum;}
InstallmentCalculator.prototype.getInterest=function(amount,monthOffset){var perCentInterest=this.interestRate*0.01;var dailyInterest=perCentInterest/365;var duration=this.interestDaysBetweenInstallments[monthOffset];return amount*dailyInterest*duration;}
InstallmentCalculator.prototype.calculateInstallmentAmount=function(){var installmentAmount=this.calculateBinarySearch(this.lowerBound,this.originalAmount*10,this.fixedDivision(this.originalAmount,this.duration),2,this.getInstallmentAmountError);if(this.currency=='CHF'){installmentAmount=this.roundUpCHF(installmentAmount);}
return installmentAmount;}
InstallmentCalculator.prototype.roundUpCHF=function(installmentAmount){var temp=+installmentAmount*100;var rounding=5-(temp%5);var result=+installmentAmount+(rounding/100);return+result.toFixed(2);}
InstallmentCalculator.prototype.updateInstallments=function(installmentAmount){for(var i=0;i<this.duration-1;i++){this.installments[i].amount=installmentAmount;}
this.installments[this.duration-1].amount=this.getLastInstallmentAmount(installmentAmount);}
InstallmentCalculator.prototype.getLastInstallmentAmount=function(installmentAmount){if(this.interestRate>0){return installmentAmount;}
var total=installmentAmount*this.duration;var roundingDiff=total-this.originalAmount;return installmentAmount-roundingDiff;}
InstallmentCalculator.prototype.calculateEffectiveInterest=function(){if(this.interestRate<=0){this.effectiveInterest=0;return;}
var effectiveInterest=this.calculateBinarySearch(this.lowerBound,this.upperBound,this.interestRate*0.01,4,this.getEffectiveInterestError);this.effectiveInterest=effectiveInterest*100;this.effectiveInterest=+this.effectiveInterest.toFixed(2);}
InstallmentCalculator.prototype.calculateFinalAmount=function(){this.finalAmount=0;for(var i=0;i<this.duration;i++){this.finalAmount=this.finalAmount+this.installments[i].amount;}
this.finalAmount=+this.finalAmount.toFixed(2);}
InstallmentCalculator.prototype.fixedDivision=function(divident,divisor){var division=divident/divisor;return+division.toFixed(2);}
function contains(array,object){for(var i=0;i<array.length;i++){if(array[i]==object){return true;}}
return false;}
function getDefaultInterestRate(duration){if(INTEREST_STRATEGY=="MATRIX_BASED"){return DEFAULT_INTEREST_RATE;}
return DURATION_BASED_INTEREST_RATES[duration];}
function getReferenceDay(fulfillmentStart,fulfillmentEnd){if(FULFILLMENT_BASE=="FULFILLMENT_START"){return fulfillmentStart;}
return fulfillmentEnd;}
function isNullOrUndefined(value){if(typeof value=="undefined"||value==null){return true;}
return false;}
function calculate(amount,duration,interestRate,currency,fulfillmentStart,fulfillmentEnd){amount=Number(amount);duration=Number(duration);interestRate=Number(interestRate);if(amount<MINIMUM_AMOUNT){throw"Amount is too low. It has to be bigger than "+MINIMUM_AMOUNT;}
if(amount>MAXIMUM_AMOUNT){throw"Amount is too high. It has to be lower than "+MAXIMUM_AMOUNT;}
if(!contains(AVAILABLE_INTEREST_RATES,interestRate)){throw"Unsupported interestRate "+interestRate+". Supported interest rates are: "+AVAILABLE_INTEREST_RATES;}
if(!contains(PRICE_MATRIX[interestRate],duration)){throw"Unsupported duration "+duration+" supported durations are: "+PRICE_MATRIX[interestRate];}
if(isNullOrUndefined(fulfillmentStart)){throw"FulfillmentStart required";}
if(isNullOrUndefined(fulfillmentEnd)){throw"FulfillmentEnd required";}
return new InstallmentCalculator(amount,duration,interestRate,currency,new payolutionXDate(getReferenceDay(fulfillmentStart,fulfillmentEnd))).calculate();}
global.Payolution={getMinimumAmount:function(){return MINIMUM_AMOUNT;},getMaximumAmount:function(){return MAXIMUM_AMOUNT;},getAvailableDurations:function(){return AVAILABLE_DURATIONS;},getAvailableInterestRates:function(){return AVAILABLE_INTEREST_RATES;},getPriceMatrix:function(){return PRICE_MATRIX;},getAvailableDurationsForInterestRate:function(interestRate){interestRate=Number(interestRate);if(!contains(AVAILABLE_INTEREST_RATES,interestRate)){return[];}
return PRICE_MATRIX[interestRate];},getDefaultInterestRate:function(interestRate){if(INTEREST_STRATEGY=="DURATION_BASED"){return null;}
return DEFAULT_INTEREST_RATE;},calculateInstallment:function(amount,duration){return calculate(amount,duration,getDefaultInterestRate(duration),null,DEFAULT_FULFILLMENT_DATE,DEFAULT_FULFILLMENT_DATE);},calculateInstallmentWithReferenceDay:function(amount,duration,fulfillmentDate){return calculate(amount,duration,getDefaultInterestRate(duration),null,fulfillmentDate,fulfillmentDate);},calculateInstallmentWithFulfillmentInfo:function(amount,duration,fulfillmentStart,fulfillmentEnd){return calculate(amount,duration,getDefaultInterestRate(duration),null,fulfillmentStart,fulfillmentEnd);},calculateInstallmentForInterestRate:function(amount,duration,interestRate){return calculate(amount,duration,interestRate,null,DEFAULT_FULFILLMENT_DATE,DEFAULT_FULFILLMENT_DATE);},calculateInstallmentForInterestRateWithReferenceDay:function(amount,duration,interestRate,fulfillmentDate){return calculate(amount,duration,interestRate,null,fulfillmentDate,fulfillmentDate);},calculateInstallmentForInterestRateWithFulfillmentInfo:function(amount,duration,interestRate,fulfillmentStart,fulfillmentEnd){return calculate(amount,duration,interestRate,null,fulfillmentStart,fulfillmentEnd);},calculateInstallmentFixedCurrency:function(amount,duration,currency){return calculate(amount,duration,getDefaultInterestRate(duration),currency,DEFAULT_FULFILLMENT_DATE,DEFAULT_FULFILLMENT_DATE);},calculateInstallmentFixedCurrencyWithReferenceDay:function(amount,duration,currency,fulfillmentDate){return calculate(amount,duration,getDefaultInterestRate(duration),currency,fulfillmentDate,fulfillmentDate);},calculateInstallmentFixedCurrencyWithFulfillmentInfo:function(amount,duration,currency,fulfillmentStart,fulfillmentEnd){return calculate(amount,duration,getDefaultInterestRate(duration),currency,fulfillmentStart,fulfillmentEnd);},calculateInstallmentFixedCurrencyWithReferenceDayAndInterestRate:function(amount,duration,interestRate,currency,fulfillmentDate){return calculate(amount,duration,interestRate,currency,fulfillmentDate,fulfillmentDate);},calculateInstallmentFixedCurrencyWithFulfillmentInfoAndInterestRate:function(amount,duration,interestRate,currency,fulfillmentStart,fulfillmentEnd){return calculate(amount,duration,interestRate,currency,fulfillmentStart,fulfillmentEnd);}}})(this);