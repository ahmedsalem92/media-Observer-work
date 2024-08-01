function InfinitiySroll(iTable, iAction, iParams) {
    $("#loading-ind_").hide();

    this.table = iTable;        // Reference to the table where data should be added
    this.action = iAction;      // Name of the conrtoller action
    this.params = iParams;      // Additional parameters to pass to the controller
    this.loading = true;       // true if asynchronous loading is in process
    this.AddTableLines = function (firstItem) {
        this.loading = true;
        this.params.firstItem = firstItem;
        // $("#footer").css("display", "block"); // show loading info
        $.ajax({
            type: 'POST',
            url: self.action,
            data: self.params,
            dataType: "html"
        })
            .done(function (result) {
                if (result) {
                    $("#" + self.table).append(result);
                    self.loading = false;
                }
            })
            .fail(function (xhr, ajaxOptions, thrownError) {
                console.log("Error in AddTableLines:", thrownError);
            })
            .always(function () {
                // $("#footer").css("display", "none"); // hide loading info
            });
    }
    $("#loading-ind_").hide();
    var self = this;
    window.onscroll = function (ev) {
        if (isScrolledIntoView("#loading-ind_")) {
            if (!self.loading) {
                $("#loading-ind_").show();
                var itemCount = $('#' + self.table + ' .isr').length;
                self.AddTableLines(itemCount);
            }
            else {
                $("#loading-ind_").hide();
            }
        }
    };
    this.AddTableLines(0);
}
function isScrolledIntoView(elem) {
    var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();

    var elemTop = $(elem).offset().top + 50;
    var elemBottom = elemTop + $(elem).height();

    return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
}