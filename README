Dumb graphing scripts that abuse Google Charts, your web browser, and your sense of good taste.

mem
===

Set the PID to the process you want to watch.

    PID=38158; \
    DATAPOINTS=400; \
    WAITTIME=50; \
    TITLE="Memory Usage"; \
    LOGFILE=mem.log; \
    open mem.html; \
    (tail -n $DATAPOINTS $LOGFILE; \
     echo 'START'; \
     while [ 1 ] ; do \
        MEM=`ps -o rss= -p $PID`; \
        echo -e "`date`\t`date +%s`\t$MEM"; \
        sleep $WAITTIME; \
     done | tee -a $LOGFILE \
    ) | php mem.php $DATAPOINTS $TITLE