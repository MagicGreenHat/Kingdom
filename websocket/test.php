#!/usr/bin/php
<?php

# Count from 1 to 10 with a sleep
for ($count = 1; $count <= 10; $count++) {
	echo $count . "\n";
	usleep(500000);
}
