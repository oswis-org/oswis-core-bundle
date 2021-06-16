<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\AddressBook;

use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;

interface ContactInterface extends NameableInterface
{
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';
    public const GENDER_UNISEX = 'unisex';

    public const TYPE_PERSON = 'person';

    public const TYPE_ORGANIZATION = 'organization';
    public const TYPE_DEPARTMENT = 'department';
    public const TYPE_UNIVERSITY = 'university';
    public const TYPE_FACULTY = 'faculty';
    public const TYPE_FACULTY_DEPARTMENT = 'faculty-department';
    public const TYPE_STUDENT_ORGANIZATION = 'student-organization';
    public const TYPE_HIGH_SCHOOL = 'high-school';
    public const TYPE_PRIMARY_SCHOOL = 'primary-school';
    public const TYPE_KINDERGARTEN = 'kindergarten';
    public const TYPE_COMPANY = 'company';

    public const COMPANY_TYPES = [self::TYPE_COMPANY];
    public const STUDENT_ORGANIZATION_TYPES = [self::TYPE_STUDENT_ORGANIZATION];
    public const SCHOOL_TYPES
        = [
            self::TYPE_UNIVERSITY,
            self::TYPE_FACULTY,
            self::TYPE_FACULTY_DEPARTMENT,
            self::TYPE_HIGH_SCHOOL,
            self::TYPE_PRIMARY_SCHOOL,
            self::TYPE_KINDERGARTEN,
        ];

    public const ORGANIZATION_TYPES
        = [
            self::TYPE_ORGANIZATION,
            self::TYPE_DEPARTMENT,
            ...self::COMPANY_TYPES,
            ...self::SCHOOL_TYPES,
            ...self::STUDENT_ORGANIZATION_TYPES,
        ];

    public const PERSON_TYPES = [self::TYPE_PERSON];
}
