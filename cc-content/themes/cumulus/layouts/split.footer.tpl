
        </div>
        <!-- END CONTENT -->


        <!-- BEGIN SIDEBAR -->
        <div id="sidebar">
            <?php View::Block ('ad300.tpl'); ?>
            <?php View::OutputSidebarBlocks(); ?>
        </div>
        <!-- END SIDEBAR -->

        <br clear="all" />

    </div>
    <!-- END MAIN -->

    <div id="footer-spacer"></div>

</div>
<!-- END WRAPPER -->

<?php View::Block ('footer_nav.tpl'); ?>

<script type="text/javascript" src="<?=THEME?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=THEME?>/js/general.js"></script>
<?php View::WriteJs(); ?>

</body>
</html>