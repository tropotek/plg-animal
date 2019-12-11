-- --------------------------------------------
-- Author: Michael Mifsud <info@tropotek.com>
-- --------------------------------------------
--
--
-- --------------------------------------------

alter table animal_type change profile_id course_id int unsigned default 0 not null;
drop index profile_id on animal_type;
create index course_id on animal_type (course_id);




