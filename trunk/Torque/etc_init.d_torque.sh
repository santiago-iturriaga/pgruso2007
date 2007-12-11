#!/bin/bash


# Source function library.
if [ -f /etc/init.d/functions ] ; then
  . /etc/init.d/functions
elif [ -f /etc/rc.d/init.d/functions ] ; then
  . /etc/rc.d/init.d/functions
  #else
  #exit 0
fi

# Read in the command arguments
case "$1" in
  start)
	echo -n $"Starting services: "
	sudo /usr/local/torque/sbin/qserverd
	echo "[TORQUE] "
	sudo /usr/local/torque/sbin/qnoded
	echo "[MOM] "
	/usr/local/maui/sbin/maui --configfile=/usr/local/maui/maui.cfg
	echo "[MAUI] "
	;;
  stop)
	echo -n $"Shutting down services: "
	/usr/local/maui/bin/schedctl -k
	echo "[MAUI] "
	sudo /usr/local/torque/bin/qterm -t quick
	echo "[TORQUE] "
	sudo /usr/local/torque/sbin/momctl -s
	echo "[MOM] "
	;;
  restart)
	$0 stop
	$0 start
	;;
  *)
	echo $"Usage: torque {start|stop|restart}"
	exit 1
esac

exit 0
