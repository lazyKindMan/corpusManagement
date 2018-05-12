$(document).ready(function () {
   $.get(
       "http://lib.hsesystem.com/lib_icd10",
       {'sdo':1,'lvl':1,'cid':1},
       function (data) {
           console.log(data);
       }
   );
});