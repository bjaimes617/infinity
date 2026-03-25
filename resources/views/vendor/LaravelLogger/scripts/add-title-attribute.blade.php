<script type="text/javascript">
    $(document).on('mouseenter', "div.activity-table table > tbody > tr > td ", function () {
        var $this = $(this);
        console.log($this);
        if (this.offsetWidth < this.scrollWidth && !$this.attr('title')) {
            $this.attr('title', $this.text());
               console.log("ejecuta",$this.text());
        }
     
    });
</script>
