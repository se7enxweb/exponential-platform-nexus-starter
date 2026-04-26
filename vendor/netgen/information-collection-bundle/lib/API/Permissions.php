<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API;

final class Permissions
{
    public const string NAME = 'infocollector';
    public const string POLICY_READ = 'read';
    public const string POLICY_DELETE = 'delete';
    public const string POLICY_EXPORT = 'export';
    public const string POLICY_ANONYMIZE = 'anonymize';
}
