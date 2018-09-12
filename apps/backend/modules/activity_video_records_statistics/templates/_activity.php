<?php
if ($activity_video_records_statistics->getActivity()):
    echo sprintf('[%s] %s', $activity_video_records_statistics->getActivity()->getId(), $activity_video_records_statistics->getActivity()->getName());
endif;
