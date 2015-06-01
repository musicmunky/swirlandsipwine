(function ($) {

	$.fn.datasort = function(options) {
		var defaults = {
			//set the default parameter values
			datatype    : 'alpha',
			sortElement : false,
			sortAttr    : false,
			reverse     : false
		},
		// combine the default and user's parameters, overriding defaults
		settings = $.extend({}, defaults, options),
		datatypes = {
			alpha : function (a, b) {
				var o = base.extract(a, b);
				return base.alpha(o.a, o.b);
			},
			number : function(a, b) {
				var o = base.extract(a, b);
				for (var e in o) {
					o[e] = o[e].replace(/[$]?(-?\d+.?\d+)/, '\$1');
				}
				return base.number(o.a, o.b);
			},
			date : function(a, b) {
				var o = base.extract(a, b);
				for (var e in o) {
					o[e] = o[e].replace(/-/g, '')
					.replace(/january|jan/i, '01')
					.replace(/february|feb/i, '02')
					.replace(/march|mar/i, '03')
					.replace(/april|apr/i, '04')
					.replace(/may/i, '05')
					.replace(/june|jun/i, '06')
					.replace(/july|jul/i, '07')
					.replace(/august|aug/i, '08')
					.replace(/september|sept|sep/i, '09')
					.replace(/october|oct/i, '10')
					.replace(/november|nov/i, '11')
					.replace(/december|dec/i, '12')
					.replace(/(\d{2}) (\d{2}), (\d{4})/, '\$3\$1\$2')
					.replace(/(\d{2})\/(\d{2})\/(\d{4})/, '\$3\$2\$1');
				}
				return base.number(o.a, o.b);
			},
			time : function(a, b) {
				var o = base.extract(a, b),
					afternoon = /^(.+) PM$/i;
				for (var e in o) {
					o[e] = o[e].split(':');
					var last = o[e].length - 1;

					if(afternoon.test(o[e][last])) {
						o[e][0] = (parseInt(o[e][0]) + 12).toString();
						o[e][last] = o[e][last].replace(afternoon, '\$1');
					}
					if(parseInt(o[e][0]) < 10 && o[e][0].length === 1) {
						o[e][0] = '0' + o[e][0];
					}
					o[e][last] = o[e][last].replace(/^(.+) AM$/i, '\$1');

					o[e] = o[e].join('');
				}
				return base.alpha(o.a, o.b);
			}
		},
		base = {
			alpha : function(a, b) {
				a = a.toUpperCase();
				b = b.toUpperCase();
				return (a < b) ? -1 : (a > b) : 1 : 0;
				//ternary operator: condition ? returnIfTrue : returnIfFalse
			},
			number : function(a, b) {
				a = parseFloat(a);
				b = parseFloat(b);
				return a - b;
			},
			extract : function (a, b) {
				var get = function (i) {
					var o = $(i);
					if (settings.sortElement) {
						o = o.children(settings.sortElement);
					}
					if (settings.sortAttr) {
						o = o.attr(settings.sortAttr);
					} else {
						o = o.text();
					}
					return o;
				};
				return {
					a : get(a),
					b : get(b)
				};
			}
		},
		that = this;

		if (typeof settings.datatype === 'string') {
			that.sort(datatypes[settings.datatype]);
		}
		if (typeof settings.datatype === 'function') {
			that.sort(settings.datatype);
		}
		if(settings.reverse) {
			that = $($.makeArray(this).reverse());
		}
		$.each(that, function(index, element) { that.parent().append(element); });
	};
})(jQuery);

/*
Examples:

$('table#myTable thead th').toggle(
	function() {
		var $this = $(this);
		$('table#myTable tbody tr').datasort({
			datatype: $this.attr('rel'),
			sortElement: 'td.' + $this.attr('class')
		});
	},
	function() {
		var $this = $(this);
		$('table#myTable tbody tr').datasort({
			datatype: $this.attr('rel'),
			sortElement: 'td.' + $this.attr('class'),
			reverse: true
		});
	}
);


$('table.a tbody tr').datasort({sortElement : 'td.last'});
$('ul.n li').datasort({datatype: 'number', reverse: true});
$('ul.curr li').datasort({ datatype: 'number' });


For sorting this:
<ul class='date'>
  <li>2009-10-06</li>
  <li>sept 25, 1995</li>
  <li>1990-06-18</li>
  <li>20100131</li>
  <li>June 18, 2009</li>
  <li>02/11/1993</li>
  <li>15941219</li>
  <li>1965-08-05</li>
  <li>1425-12-25</li>
</ul>
USE THIS:
$('ul.date li').datasort({datatype: 'date'});



For sorting this:
<ul class='time'>
  <li>1:15:47</li>
  <li>3:45 PM</li>
  <li>12:00:17</li>
  <li>06:56</li>
  <li>19:39</li>
  <li>4:32 AM</li>
  <li>00:15:36</li>
</ul>
USE THIS:
$('ul.time li').datasort({datatype: 'time'});



For sorting this:
<ul class="rating">
  <li>Good</li>
  <li>Excellent</li>
  <li>Poor</li>
  <li>Satisfactory</li>
</ul>
USE THIS:
$('ul.rating li').datasort({datatype: function(a, b) {
      var o  = {
      a : $(a).text(),
      b : $(b).text()
      }
      for (var e in o) {
        o[e] = o[e].replace(/poor/i, 0)
                   .replace(/satisfactory/i, 1)
                   .replace(/good/i, 2)
                   .replace(/excellent/i, 3);
      }
      return o.a - o.b;
    }
});
*/