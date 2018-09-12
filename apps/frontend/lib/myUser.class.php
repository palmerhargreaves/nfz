<?php

class myUser extends BasicAuthUser
{
    public function getCurrentQuarter() {
        return $this->getAttribute('current_q', D::getQuarter(D::calcQuarterData(time())), BaseActivityActions::FILTER_Q_NAMESPACE);
    }

    public function getCurrentYear() {
        return $this->getAttribute('current_year', D::getYear(D::calcQuarterData(time())), BaseActivityActions::FILTER_YEAR_NAMESPACE);
    }
}
