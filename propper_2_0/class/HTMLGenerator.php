<?php

    class HTMLTools{
        function printTab($nbtab){
            $retval = "";
            for($i=1;$i<=$nbtab;$i++){
                $retval .= "\t";
            }

            return $retval;
        }
    
        function HTMLTools()  /* PHP 4 constructor */
        {
            // generate references (see http://latrine.dgx.cz/how-to-emulate-php5-object-model-in-php4)
            foreach ($this as $key => $foo) $GLOBALS['$$HIDDEN$$'][] = & $this->$key;

            // call php5 constructor
            if (method_exists($this, '__construct')) {
                $args = func_get_args();
                call_user_func_array(array(&$this, '__construct'), $args);
            }
        }
}

    /**/
    class MenuBuilder{

        function printReportMenu($selected) /* static */{
            ?>
                    <!-- start navigation -->
                    <div id="navigation">
                        <ul id="nav">
                            <li><a href="index.php" title="PNP Assessment | Home" accesskey="1" id="navhome" <? if($selected==0){echo "class='selected'";} ?>>Home</a></li>
                            <li><a href="report_per_cc.php" title="Report Call Center" accesskey="2" id="navzykl" <? if($selected==1){echo "class='selected'";} ?>>Call Center</a></li>
                            <li><a href="report_per_grapari.php" title="Report GraPARI" accesskey="3" id="navnews" <? if($selected==2){echo "class='selected'";} ?>>GraPARI</a></li>
                            <li><a href="report_per_gerai.php" title="Report Gerai" accesskey="4" id="navpres" <? if($selected==3){echo "class='selected'";} ?>>Gerai</a></li>
                            <li><a href="report_per_plasa.php" title="Report Plasa" accesskey="5" id="navkarr" <? if($selected==4){echo "class='selected'";} ?>>Plasa</a></li>
				<li><a href="report_per_webservice.php" title="Report Web Service" accesskey="6" id="navkarr" <? if($selected==5){echo "class='selected'";} ?>>Web Service</a></li>
                        </ul>
                        <span id="navright">&nbsp;</span>
                    </div><!-- ende navigation -->
            <?php
        }

	function printReportMenuSOP($selected) /* static */{
            ?>
                    <!-- start navigation -->
                    <div id="navigation">
                        <ul id="nav">
                            <li><a href="index.php" title="PNP Assessment | Home" accesskey="1" id="navhome" <? if($selected==0){echo "class='selected'";} ?>>Home</a></li>
                            <li><a href="report_sop_cc.php" title="Report Call Center" accesskey="2" id="navzykl" <? if($selected==1){echo "class='selected'";} ?>>Call Center</a></li>
                            <li><a href="report_sop.php" title="Report GraPARI" accesskey="3" id="navnews" <? if($selected==2){echo "class='selected'";} ?>>GraPARI</a></li>
                            <li><a href="report_sop_gerai.php" title="Report Gerai" accesskey="4" id="navpres" <? if($selected==3){echo "class='selected'";} ?>>Gerai</a></li>
                            <li><a href="report_sop_plasa.php" title="Report Plasa" accesskey="5" id="navkarr" <? if($selected==4){echo "class='selected'";} ?>>Plasa</a></li>
                        </ul>
                        <span id="navright">&nbsp;</span>
                    </div><!-- ende navigation -->
            <?php
        }

        function printTopMenu($access, $selected) /* static */{
            if($access == "GSPV"){
            ?>
                    <!-- start navigation -->
                    <div id="navigation">
                        <ul id="nav">
                            <li><a href="main.php" title="PNP Assessment | Home" accesskey="1" id="navhome" class="selected">Home</a></li>
                            <li><a href="report_most_wrong_per_grapari.php" title="Jawaban Salah" accesskey="2" id="navzykl">Jawaban Salah</a></li>
                            <li><a href="report_per_grapari.php" title="Report" accesskey="3" id="navnews">Report</a></li>
                            <li><a href="help.php" title="Help" accesskey="4" id="navpres">Help</a></li>
                            <li><a href="logout.php" title="Logout" accesskey="5" id="navkarr">Logout</a></li>
                        </ul>
                        <span id="navright">&nbsp;</span>
                    </div><!-- ende navigation -->
            <?php
            }
	     if($access == "GRPV"){
            ?>
                    <!-- start navigation -->
                    <div id="navigation">
                        <ul id="nav">
                            <li><a href="main.php" title="PNP Assessment | Home" accesskey="1" id="navhome" class="selected">Home</a></li>
                            <li><a href="report_most_wrong_per_grapari.php" title="Jawaban Salah" accesskey="2" id="navzykl">Jawaban Salah</a></li>
                            <li><a href="report_per_grapari.php" title="Report" accesskey="3" id="navnews">Report</a></li>
                            <li><a href="help.php" title="Help" accesskey="4" id="navpres">Help</a></li>
                            <li><a href="logout.php" title="Logout" accesskey="5" id="navkarr">Logout</a></li>
                        </ul>
                        <span id="navright">&nbsp;</span>
                    </div><!-- ende navigation -->
            <?php
            }
	     if($access == "BOGH"){
            ?>
                    <!-- start navigation -->
                    <div id="navigation">
                        <ul id="nav">
                            <li><a href="main.php" title="PNP Assessment | Home" accesskey="1" id="navhome" class="selected">Home</a></li>
                            <li><a href="report_most_wrong_per_grapari.php" title="Jawaban Salah" accesskey="2" id="navzykl">Jawaban Salah</a></li>
                            <li><a href="report_per_grapari.php" title="Report" accesskey="3" id="navnews">Report</a></li>
                            <li><a href="help.php" title="Help" accesskey="4" id="navpres">Help</a></li>
                            <li><a href="logout.php" title="Logout" accesskey="5" id="navkarr">Logout</a></li>
                        </ul>
                        <span id="navright">&nbsp;</span>
                    </div><!-- ende navigation -->
            <?php
            }
            if($access == "CSPV"){
            ?>
                    <!-- start navigation -->
                    <div id="navigation">
                        <ul id="nav">
                            <li><a href="main.php" title="PNP Assessment | Home" accesskey="1" id="navhome" class="selected">Home</a></li>
                            <li><a href="report_most_wrong_per_callcenter.php" title="Jawaban Salah" accesskey="2" id="navzykl">Jawaban Salah</a></li>
                            <li><a href="report_per_callcenter.php" title="Report" accesskey="3" id="navnews">Report</a></li>
                            <li><a href="help.php" title="Help" accesskey="4" id="navpres">Help</a></li>
                            <li><a href="logout.php" title="Logout" accesskey="5" id="navkarr">Logout</a></li>
                        </ul>
                        <span id="navright">&nbsp;</span>
                    </div><!-- ende navigation -->
            <?php
            }
            if($access == "CSAG"){
            ?>
                    <!-- start navigation -->
                    <div id="navigation">
                        <ul id="nav">
                            <li><a href="main.php" title="PNP Assessment | Home" accesskey="1" id="navhome" class="selected">Home</a></li>
                            <li><a href="report_per_callcenter.php" title="Report" accesskey="2" id="navnews">Report</a></li>
                            <li><a href="help.php" title="Help" accesskey="3" id="navpres">Help</a></li>
                            <li><a href="logout.php" title="Logout" accesskey="4" id="navkarr">Logout</a></li>
                        </ul>
                        <span id="navright">&nbsp;</span>
                    </div><!-- ende navigation -->
            <?php
            }
	     if($access == "T1RQ"){
            ?>
                    <!-- start navigation -->
                    <div id="navigation">
                        <ul id="nav">
                            <li><a href="main.php" title="PNP Assessment | Home" accesskey="1" id="navhome" class="selected">Home</a></li>
                            <li><a href="help.php" title="Help" accesskey="2" id="navpres">Help</a></li>
                            <li><a href="logout.php" title="Logout" accesskey="3" id="navkarr">Logout</a></li>
                        </ul>
                        <span id="navright">&nbsp;</span>
                    </div><!-- ende navigation -->
            <?php
            }

            if($access == "H4JQ"){
            ?>
                    <!-- start navigation -->
                    <div id="navigation">
                        <ul id="nav">
                            <li><a href="main.php" title="PNP Assessment | Home" accesskey="1" id="navhome" class="selected">Home</a></li>
                            <li><a href="help.php" title="Help" accesskey="2" id="navpres">Help</a></li>
                            <li><a href="logout.php" title="Logout" accesskey="3" id="navkarr">Logout</a></li>
                        </ul>
                        <span id="navright">&nbsp;</span>
                    </div><!-- ende navigation -->
            <?php
            }

		if($access == "HAJQ"){
            ?>
                    <!-- start navigation -->
                    <div id="navigation">
                        <ul id="nav">
                            <li><a href="main.php" title="PNP Assessment | Home" accesskey="1" id="navhome" class="selected">Home</a></li>
                            <li><a href="help.php" title="Help" accesskey="2" id="navpres">Help</a></li>
                            <li><a href="logout.php" title="Logout" accesskey="3" id="navkarr">Logout</a></li>
                        </ul>
                        <span id="navright">&nbsp;</span>
                    </div><!-- ende navigation -->
            <?php
            }

		if($access == "BCRQ"){
            ?>
                    <!-- start navigation -->
                    <div id="navigation">
                        <ul id="nav">
                            <li><a href="main.php" title="PNP Assessment | Home" accesskey="1" id="navhome" class="selected">Home</a></li>
                            <li><a href="help.php" title="Help" accesskey="2" id="navpres">Help</a></li>
                            <li><a href="logout.php" title="Logout" accesskey="3" id="navkarr">Logout</a></li>
                        </ul>
                        <span id="navright">&nbsp;</span>
                    </div><!-- ende navigation -->
            <?php
            }
	     
	     if($access == "ND2Q"){
            ?>
                    <!-- start navigation -->
                    <div id="navigation">
                        <ul id="nav">
                            <li><a href="main.php" title="PNP Assessment | Home" accesskey="1" id="navhome" class="selected">Home</a></li>
                            <li><a href="help.php" title="Help" accesskey="2" id="navpres">Help</a></li>
                            <li><a href="logout.php" title="Logout" accesskey="3" id="navkarr">Logout</a></li>
                        </ul>
                        <span id="navright">&nbsp;</span>
                    </div><!-- ende navigation -->
            <?php
            }
		
	     if($access == "PH0Q"){
            ?>
                    <!-- start navigation -->
                    <div id="navigation">
                        <ul id="nav">
                            <li><a href="main.php" title="PNP Assessment | Home" accesskey="1" id="navhome" class="selected">Home</a></li>
                            <li><a href="help.php" title="Help" accesskey="2" id="navpres">Help</a></li>
                            <li><a href="logout.php" title="Logout" accesskey="3" id="navkarr">Logout</a></li>
                        </ul>
                        <span id="navright">&nbsp;</span>
                    </div><!-- ende navigation -->
            <?php
            }
        }

     
        function MenuBuilder()  /* PHP 4 constructor */
        {
            // generate references (see http://latrine.dgx.cz/how-to-emulate-php5-object-model-in-php4)
            foreach ($this as $key => $foo) $GLOBALS['$$HIDDEN$$'][] = & $this->$key;

            // call php5 constructor
            if (method_exists($this, '__construct')) {
                $args = func_get_args();
                call_user_func_array(array(&$this, '__construct'), $args);
            }
        }
}
?>      