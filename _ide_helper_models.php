<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * Modelo Achievement para sistema de gamificación de KiroLux.
 * 
 * Gestiona logros, medallas y objetivos que los usuarios pueden
 * desbloquear mediante acciones relacionadas con energía renovable,
 * ahorro energético y participación en cooperativas.
 *
 * @property int $id
 * @property string $name Nombre del logro
 * @property string $slug Slug único
 * @property string $description Descripción del logro
 * @property string|null $icon Icono del logro
 * @property string $badge_color Color del badge en HEX
 * @property string $category Categoría del logro
 * @property string $type Tipo de logro (single, progressive, recurring)
 * @property string $difficulty Dificultad (bronze, silver, gold, platinum, legendary)
 * @property array|null $conditions Condiciones para obtener el logro
 * @property int $points Puntos que otorga
 * @property int|null $required_value Valor requerido
 * @property string|null $required_unit Unidad del valor requerido
 * @property bool $is_active Si está activo
 * @property bool $is_hidden Si es un logro secreto
 * @property int $sort_order Orden de visualización
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $completedByUsers
 * @property-read int|null $completed_by_users_count
 * @property-read string $category_name
 * @property-read string $difficulty_name
 * @property-read array $stats
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserAchievement> $userAchievements
 * @property-read int|null $user_achievements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement byCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement byDifficulty(string $difficulty)
 * @method static \Database\Factories\AchievementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement visible()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereBadgeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereIsHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereRequiredUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereRequiredValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereUpdatedAt($value)
 */
	class Achievement extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $activity_type
 * @property string $related_type
 * @property int $related_id
 * @property array<array-key, mixed> $activity_data
 * @property string|null $description
 * @property string|null $summary
 * @property numeric|null $energy_amount_kwh
 * @property numeric|null $cost_savings_eur
 * @property numeric|null $co2_savings_kg
 * @property numeric|null $investment_amount_eur
 * @property int|null $community_impact_score
 * @property string $visibility
 * @property bool $is_featured
 * @property bool $is_milestone
 * @property bool $notify_followers
 * @property bool $show_in_feed
 * @property bool $allow_interactions
 * @property int $engagement_score
 * @property-read int|null $likes_count
 * @property-read int|null $loves_count
 * @property int $wow_count
 * @property int $comments_count
 * @property-read int|null $shares_count
 * @property-read int|null $bookmarks_count
 * @property int $views_count
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property string|null $location_name
 * @property \Illuminate\Support\Carbon|null $activity_occurred_at
 * @property bool $is_real_time
 * @property string|null $activity_group
 * @property int|null $parent_activity_id
 * @property numeric $relevance_score
 * @property \Illuminate\Support\Carbon|null $boost_until
 * @property array<array-key, mixed>|null $algorithm_data
 * @property string $status
 * @property int $flags_count
 * @property array<array-key, mixed>|null $flag_reasons
 * @property int|null $moderated_by
 * @property \Illuminate\Support\Carbon|null $moderated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialInteraction> $bookmarks
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ActivityFeed> $childActivities
 * @property-read int|null $child_activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialInteraction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialInteraction> $likes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialInteraction> $loves
 * @property-read \App\Models\User|null $moderator
 * @property-read ActivityFeed|null $parentActivity
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $related
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialInteraction> $shares
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed communityRelated()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed energyRelated()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed feedFor(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed highEngagement(int $minScore = 100)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed inDateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed milestones()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed nearLocation(float $lat, float $lng, int $radiusKm = 50)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed projectRelated()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed visibleFor(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereActivityData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereActivityGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereActivityOccurredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereActivityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereAlgorithmData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereAllowInteractions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereBookmarksCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereBoostUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereCo2SavingsKg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereCommunityImpactScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereCostSavingsEur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereEnergyAmountKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereEngagementScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereFlagReasons($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereFlagsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereInvestmentAmountEur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereIsMilestone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereIsRealTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereLocationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereLovesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereModeratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereModeratedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereNotifyFollowers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereParentActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereRelatedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereRelevanceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereSharesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereShowInFeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereViewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityFeed whereWowCount($value)
 */
	class ActivityFeed extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property bool $is_primary
 * @property int $person_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Person $person
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alias whereUpdatedAt($value)
 */
	class Alias extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $day
 * @property int $month
 * @property string|null $year
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Database\Factories\AnniversaryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anniversary whereYear($value)
 */
	class Anniversary extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $scope
 * @property int $rate_limit
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $is_revoked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereIsRevoked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereRateLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereUserId($value)
 */
	class ApiKey extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $slogan
 * @property string|null $primary_color
 * @property string|null $secondary_color
 * @property string $locale
 * @property string|null $custom_js
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Organization $organization
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereCustomJs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereSlogan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting whereUpdatedAt($value)
 */
	class AppSetting extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $person_id
 * @property float|null $height_cm
 * @property float|null $weight_kg
 * @property string|null $body_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Person $person
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereBodyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereHeightCm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereWeightKg($value)
 */
	class Appearance extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $genre
 * @property int|null $person_id
 * @property string|null $stage_name
 * @property string|null $group_name
 * @property int|null $active_years_start
 * @property int|null $active_years_end
 * @property string|null $bio
 * @property string|null $photo
 * @property array<array-key, mixed>|null $social_links
 * @property int|null $language_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \App\Models\Language|null $language
 * @property-read \App\Models\Person|null $person
 * @method static \Database\Factories\ArtistFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereActiveYearsEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereActiveYearsStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereGenre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereSocialLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereStageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereUpdatedAt($value)
 */
	class Artist extends \Eloquent {}
}

namespace App\Models{
/**
 * Class AutonomousCommunity
 * 
 * Represents an autonomous community (Spain).
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $code
 * @property int|null $country_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $area_km2
 * @property float|null $altitude_m
 * @property int|null $timezone_id
 * @property-read Country $country
 * @property-read Timezone $timezone
 * @property-read \Illuminate\Database\Eloquent\Collection|Province[] $provinces
 * @property-read \Illuminate\Database\Eloquent\Collection|Region[] $regions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Municipality> $municipalities
 * @property-read int|null $municipalities_count
 * @property-read int|null $provinces_count
 * @property-read int|null $regions_count
 * @method static \Database\Factories\AutonomousCommunityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereAltitudeM($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereAreaKm2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutonomousCommunity whereUpdatedAt($value)
 */
	class AutonomousCommunity extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $awarded_by
 * @property string|null $first_year_awarded
 * @property string|null $category
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AwardWinner> $awardWinners
 * @property-read int|null $award_winners_count
 * @method static \Database\Factories\AwardFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereAwardedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereFirstYearAwarded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereUpdatedAt($value)
 */
	class Award extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $person_id
 * @property int $award_id
 * @property int|null $year
 * @property string $classification
 * @property int|null $work_id
 * @property int|null $municipality_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Award $award
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\Work|null $work
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereAwardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereWorkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardWinner whereYear($value)
 */
	class AwardWinner extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $date
 * @property string $slug
 * @property int|null $municipality_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CalendarHolidayLocation> $locations
 * @property-read int|null $locations_count
 * @property-read \App\Models\Municipality|null $municipality
 * @method static \Database\Factories\CalendarHolidayFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHoliday whereUpdatedAt($value)
 */
	class CalendarHoliday extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $calendar_holiday_id
 * @property int|null $municipality_id
 * @property int|null $province_id
 * @property int|null $autonomous_community_id
 * @property int|null $country_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AutonomousCommunity|null $autonomousCommunity
 * @property-read \App\Models\Country|null $country
 * @property-read \App\Models\CalendarHoliday $holiday
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \App\Models\Province|null $province
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereAutonomousCommunityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereCalendarHolidayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CalendarHolidayLocation whereUpdatedAt($value)
 */
	class CalendarHolidayLocation extends \Eloquent {}
}

namespace App\Models{
/**
 * Cálculo de huella de carbono realizado por usuarios.
 * 
 * Registra los cálculos de CO2 realizados por usuarios o sistemas
 * para poder hacer seguimiento, estadísticas y recomendaciones.
 *
 * @property int $id
 * @property int|null $user_id Usuario que realiza el cálculo
 * @property int $carbon_equivalence_id Equivalencia utilizada
 * @property float $quantity Cantidad utilizada
 * @property float $co2_result CO2 calculado en kg
 * @property string|null $context Contexto del cálculo
 * @property array|null $parameters Parámetros adicionales
 * @property string|null $session_id ID de sesión para usuarios anónimos
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\CarbonEquivalence $carbonEquivalence
 * @property-read mixed $compensation_recommendations
 * @property-read mixed $impact_level
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation forSession($sessionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation forUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation recent($days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation whereCarbonEquivalenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation whereCo2Result($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation whereParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonCalculation whereUserId($value)
 */
	class CarbonCalculation extends \Eloquent {}
}

namespace App\Models{
/**
 * Equivalencias de carbono para calcular huella ambiental.
 * 
 * Permite calcular el impacto en CO2 de diferentes actividades,
 * productos, servicios y procesos energéticos para fomentar
 * la sostenibilidad y compensación ambiental.
 *
 * @property int $id
 * @property string $name Nombre del elemento/actividad
 * @property string $slug Slug único para URLs
 * @property float $co2_kg_equivalent CO2 equivalente en kg
 * @property string|null $description Descripción detallada
 * @property string $category Categoría: energy, transport, food, etc
 * @property string $unit Unidad de medida: kwh, km, kg, etc
 * @property float|null $efficiency_ratio Ratio de eficiencia
 * @property float|null $loss_factor Factor de pérdida
 * @property string|null $calculation_method Método de cálculo
 * @property array|null $calculation_params Parámetros adicionales
 * @property string $source Fuente de los datos
 * @property string|null $source_url URL de la fuente
 * @property bool $is_verified Si está verificado oficialmente
 * @property string|null $verification_entity Entidad verificadora
 * @property \Carbon\Carbon|null $last_updated Última actualización
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CarbonSavingLog[] $savingLogs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CarbonCalculation[] $calculations
 * @property-read int|null $calculations_count
 * @property-read mixed $category_name
 * @property-read mixed $common_equivalences
 * @property-read mixed $compensation_recommendations
 * @property-read mixed $impact_color
 * @property-read mixed $impact_level
 * @property-read mixed $is_high_impact
 * @property-read mixed $is_low_impact
 * @property-read int|null $saving_logs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence energy()
 * @method static \Database\Factories\CarbonEquivalenceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence food()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence ofCategory($category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence transport()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence verified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereCalculationMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereCalculationParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereCo2KgEquivalent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereEfficiencyRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereLossFactor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereSourceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonEquivalence whereVerificationEntity($value)
 */
	class CarbonEquivalence extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $cooperative_id
 * @property numeric $kw_installed
 * @property numeric|null $production_kwh
 * @property numeric|null $co2_saved_kg
 * @property \Illuminate\Support\Carbon $date_range_start
 * @property \Illuminate\Support\Carbon|null $date_range_end
 * @property string|null $estimation_source
 * @property string|null $carbon_saving_method
 * @property bool $created_by_system
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cooperative|null $cooperative
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CarbonEquivalence> $equivalences
 * @property-read int|null $equivalences_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog byCooperative($cooperativeId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog byDateRange($startDate, $endDate = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog systemGenerated()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog userGenerated()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereCarbonSavingMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereCo2SavedKg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereCooperativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereCreatedBySystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereDateRangeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereDateRangeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereEstimationSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereKwInstalled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereProductionKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingLog whereUserId($value)
 */
	class CarbonSavingLog extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property numeric $installation_power_kw
 * @property numeric|null $production_kwh
 * @property int|null $municipality_id
 * @property int|null $province_id
 * @property string $period
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property numeric $efficiency_ratio
 * @property numeric $loss_factor
 * @property string|null $estimated_production_kwh
 * @property string|null $co2_saved_kg
 * @property string|null $money_saved_eur
 * @property int|null $trees_equivalent
 * @property string|null $equivalences
 * @property string $calculation_method
 * @property string|null $emission_factor_used
 * @property string|null $electricity_price_used
 * @property string|null $calculation_parameters
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \App\Models\Province|null $province
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest byMunicipality($municipalityId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest byPeriod($period)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest byProvince($provinceId)
 * @method static \Database\Factories\CarbonSavingRequestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereCalculationMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereCalculationParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereCo2SavedKg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereEfficiencyRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereElectricityPriceUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereEmissionFactorUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereEquivalences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereEstimatedProductionKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereInstallationPowerKw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereLossFactor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereMoneySavedEur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereProductionKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereTreesEquivalent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarbonSavingRequest withRegionalFactors()
 */
	class CarbonSavingRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon
 * @property string|null $color
 * @property string $type
 * @property int|null $parent_id
 * @property bool $is_active
 * @property bool $is_featured
 * @property int $sort_order
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read string $full_name
 * @property-read Category|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category active()
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category root()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category withParent()
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * Modelo para el Santoral Católico.
 * 
 * Gestiona información completa sobre santos católicos, incluyendo
 * fechas de celebración, patronazgos, biografías y relaciones
 * con municipios y otros modelos del sistema.
 *
 * @property int $id
 * @property string $name Nombre del santo
 * @property string|null $canonical_name Nombre canónico en latín
 * @property string $slug Slug único para URL
 * @property string|null $description Descripción breve del santo
 * @property string|null $biography Biografía completa del santo
 * @property \Carbon\Carbon|null $birth_date Fecha de nacimiento
 * @property \Carbon\Carbon|null $death_date Fecha de muerte/tránsito
 * @property \Carbon\Carbon|null $canonization_date Fecha de canonización
 * @property \Carbon\Carbon $feast_date Fecha de celebración litúrgica
 * @property \Carbon\Carbon|null $feast_date_optional Fecha alternativa de celebración
 * @property string $category Categoría del santo (martyr, confessor, virgin, etc.)
 * @property string $feast_type Tipo de celebración litúrgica
 * @property string|null $liturgical_color Color litúrgico de la celebración
 * @property string|null $patron_of Patrono de (oficios, lugares, causas)
 * @property bool $is_patron Es patrono de algún lugar o causa
 * @property array|null $patronages Lista de patronazgos específicos
 * @property string|null $specialties Especialidades o virtudes del santo
 * @property int|null $birth_place_id Lugar de nacimiento
 * @property int|null $death_place_id Lugar de muerte
 * @property int|null $municipality_id Municipio donde es patrono
 * @property string|null $region Región o territorio de influencia
 * @property string|null $country País de origen o influencia
 * @property string|null $liturgical_rank Rango litúrgico
 * @property string|null $prayers Oraciones asociadas al santo
 * @property string|null $hymns Himnos asociados al santo
 * @property array|null $attributes Atributos o símbolos del santo
 * @property bool $is_active Santo activo en el calendario
 * @property bool $is_universal Celebrado universalmente
 * @property bool $is_local Solo celebrado localmente
 * @property int $popularity_score Puntuación de popularidad
 * @property string|null $notes Notas adicionales
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \App\Models\Municipality|null $birthPlace
 * @property-read \App\Models\Municipality|null $deathPlace
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CalendarHoliday[] $calendarHolidays
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Anniversary[] $anniversaries
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Person[] $people
 * @property-read int|null $anniversaries_count
 * @property-read int|null $calendar_holidays_count
 * @property-read int|null $age_at_death
 * @property-read string $category_name
 * @property-read int $days_until_next_feast
 * @property-read string $feast_type_name
 * @property-read string $liturgical_color_name
 * @property-read \Carbon\Carbon $next_feast_date
 * @property-read int|null $years_since_canonization
 * @property-read int|null $people_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint byCategory($category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint byFeastDate($date)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint byFeastType($feastType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint byMunicipality($municipalityId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint local()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint patrons()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint popular($minScore = 5)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint universal()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereBiography($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereBirthPlaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereCanonicalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereCanonizationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereDeathDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereDeathPlaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereFeastDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereFeastDateOptional($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereFeastType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereHymns($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereIsLocal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereIsPatron($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereIsUniversal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereLiturgicalColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereLiturgicalRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint wherePatronOf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint wherePatronages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint wherePopularityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint wherePrayers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereSpecialties($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatholicSaint whereUpdatedAt($value)
 */
	class CatholicSaint extends \Eloquent {}
}

namespace App\Models{
/**
 * Modelo Challenge para retos energéticos de KiroLux.
 * 
 * Gestiona challenges individuales, comunitarios y cooperativos
 * relacionados con ahorro energético, producción solar y
 * sostenibilidad ambiental.
 *
 * @property int $id
 * @property string $name Nombre del reto
 * @property string $slug Slug único
 * @property string $description Descripción del reto
 * @property string|null $instructions Instrucciones detalladas
 * @property string|null $icon Icono del reto
 * @property string $banner_color Color del banner
 * @property string $type Tipo (individual, community, cooperative)
 * @property string $category Categoría del reto
 * @property string $difficulty Dificultad
 * @property \Carbon\Carbon $start_date Fecha de inicio
 * @property \Carbon\Carbon $end_date Fecha de fin
 * @property array|null $goals Objetivos del reto
 * @property array|null $rewards Recompensas
 * @property int|null $max_participants Máximo participantes
 * @property int $min_participants Mínimo participantes
 * @property float $entry_fee Cuota de entrada
 * @property float $prize_pool Premio acumulado
 * @property bool $is_active Si está activo
 * @property bool $is_featured Si está destacado
 * @property bool $auto_join Auto-inscripción
 * @property int $sort_order Orden
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $activeParticipants
 * @property-read int|null $active_participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $completedParticipants
 * @property-read int|null $completed_participants_count
 * @property-read string $category_name
 * @property-read string $difficulty_name
 * @property-read array $stats
 * @property-read string $type_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserChallenge> $userChallenges
 * @property-read int|null $user_challenges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge byCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge byDifficulty(string $difficulty)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge byType(string $type)
 * @method static \Database\Factories\ChallengeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge finished()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge ongoing()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge upcoming()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereAutoJoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereBannerColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereEntryFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereGoals($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereMaxParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereMinParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge wherePrizePool($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereRewards($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Challenge whereUpdatedAt($value)
 */
	class Challenge extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $hex_code
 * @property string|null $rgb_code
 * @property string|null $hsl_code
 * @property bool $is_primary
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $artists
 * @property-read int|null $artists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cooperative> $cooperatives
 * @property-read int|null $cooperatives_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Festival> $festivals
 * @property-read int|null $festivals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VisualIdentity> $visualIdentities
 * @property-read int|null $visual_identities_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereHexCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereHslCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereRgbCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Color whereUpdatedAt($value)
 */
	class Color extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyType whereUpdatedAt($value)
 */
	class CompanyType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $consultant_id
 * @property int $client_id
 * @property string $title
 * @property string $description
 * @property string $type
 * @property string $format
 * @property string $status
 * @property numeric|null $hourly_rate
 * @property numeric|null $fixed_price
 * @property numeric|null $total_amount
 * @property string $currency
 * @property int|null $estimated_hours
 * @property int|null $actual_hours
 * @property \Illuminate\Support\Carbon $requested_at
 * @property \Illuminate\Support\Carbon|null $accepted_at
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $deadline
 * @property array<array-key, mixed> $requirements
 * @property array<array-key, mixed> $deliverables
 * @property array<array-key, mixed>|null $milestones
 * @property string|null $client_notes
 * @property string|null $consultant_notes
 * @property int|null $client_rating
 * @property int|null $consultant_rating
 * @property string|null $client_review
 * @property string|null $consultant_review
 * @property numeric $platform_commission
 * @property bool $is_featured
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $client
 * @property-read \App\Models\User $consultant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService accepted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService byClient(int $clientId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService byConsultant(int $consultantId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService byFormat(string $format)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService inProgress()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService overdue()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService requested()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereActualHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereClientNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereClientRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereClientReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereConsultantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereConsultantNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereConsultantRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereConsultantReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereDeliverables($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereEstimatedHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereFixedPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereHourlyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereMilestones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService wherePlatformCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereRequestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereRequirements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsultationService whereUpdatedAt($value)
 */
	class ConsultationService extends \Eloquent {}
}

namespace App\Models{
/**
 * Relación polimórfica entre hashtags y contenido.
 * 
 * Permite asociar hashtags a cualquier tipo de contenido
 * con métricas de relevancia y engagement.
 *
 * @property int $id
 * @property int $hashtag_id
 * @property string $hashtaggable_type
 * @property int $hashtaggable_id
 * @property int $added_by
 * @property int $clicks_count
 * @property numeric $relevance_score
 * @property bool $is_auto_generated
 * @property numeric|null $confidence_score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $addedBy
 * @property-read \App\Models\Hashtag $hashtag
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $hashtaggable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag whereClicksCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag whereConfidenceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag whereHashtagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag whereHashtaggableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag whereHashtaggableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag whereIsAutoGenerated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag whereRelevanceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentHashtag whereUpdatedAt($value)
 */
	class ContentHashtag extends \Eloquent {}
}

namespace App\Models{
/**
 * Voto en contenido (upvote/downvote estilo Reddit/StackOverflow).
 * 
 * Sistema de votación para posts, comentarios, proyectos, etc.
 * con peso basado en reputación del votante.
 *
 * @property int $id
 * @property int $user_id
 * @property string $votable_type
 * @property int $votable_id
 * @property string $vote_type
 * @property int $vote_weight
 * @property string|null $reason
 * @property bool $is_helpful_vote
 * @property array<array-key, mixed>|null $metadata
 * @property bool $is_valid
 * @property int|null $validated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\User|null $validator
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $votable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote whereIsHelpfulVote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote whereIsValid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote whereValidatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote whereVotableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote whereVotableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote whereVoteType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContentVote whereVoteWeight($value)
 */
	class ContentVote extends \Eloquent {}
}

namespace App\Models{
/**
 * Cooperativa energética o de otro tipo.
 * 
 * Representa una cooperativa que puede ofrecer servicios energéticos,
 * gestionar instalaciones de autoconsumo, o realizar otras actividades cooperativas.
 *
 * @property int $id
 * @property string $name Nombre de la cooperativa
 * @property string $slug Slug único para URLs
 * @property string|null $legal_name Nombre legal/social
 * @property string $cooperative_type Tipo: energy, housing, agriculture, etc
 * @property string $scope Ámbito: local, regional, national
 * @property string|null $nif NIF/CIF de la cooperativa
 * @property \Carbon\Carbon|null $founded_at Fecha de fundación
 * @property string $phone Teléfono de contacto
 * @property string $email Email de contacto
 * @property string $website Web oficial
 * @property string|null $logo_url URL del logo
 * @property int|null $image_id Imagen asociada
 * @property int $municipality_id Municipio donde se ubica
 * @property string $address Dirección física
 * @property float|null $latitude Latitud para geolocalización
 * @property float|null $longitude Longitud para geolocalización
 * @property string|null $description Descripción de la cooperativa
 * @property int|null $number_of_members Número de socios
 * @property string $main_activity Actividad principal
 * @property bool $is_open_to_new_members Si acepta nuevos socios
 * @property string $source Fuente de los datos
 * @property int|null $data_source_id Fuente de datos estructurada
 * @property bool $has_energy_market_access Si tiene acceso al mercado energético
 * @property string|null $legal_form Forma jurídica específica
 * @property string|null $statutes_url URL de los estatutos
 * @property bool $accepts_new_installations Si acepta nuevas instalaciones
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Municipality $municipality Municipio
 * @property-read \App\Models\Image|null $image Imagen
 * @property-read \App\Models\DataSource|null $dataSource Fuente de datos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CooperativeUserMember[] $userMemberships Membresías
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users Usuarios socios
 * @property-read mixed $active_members
 * @property-read mixed $contact_summary
 * @property-read mixed $cooperative_type_name
 * @property-read mixed $is_active_for_projects
 * @property-read mixed $scope_name
 * @property-read mixed $years_since_founded
 * @property-read int|null $user_memberships_count
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative acceptingInstallations()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative energy()
 * @method static \Database\Factories\CooperativeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative inMunicipality($municipalityId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative ofScope($scope)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative ofType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative openToNewMembers()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereAcceptsNewInstallations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereCooperativeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereDataSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereFoundedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereHasEnergyMarketAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereIsOpenToNewMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereLegalForm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereLegalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereMainActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereNif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereNumberOfMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereStatutesUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative withEnergyMarketAccess()
 */
	class Cooperative extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $cooperative_id
 * @property int $author_id
 * @property string $title
 * @property string $content
 * @property string $post_type
 * @property string $status
 * @property string $visibility
 * @property array<array-key, mixed>|null $attachments
 * @property array<array-key, mixed>|null $metadata
 * @property bool $comments_enabled
 * @property bool $is_pinned
 * @property bool $is_featured
 * @property int $views_count
 * @property int $likes_count
 * @property int $comments_count
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $pinned_until
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $author
 * @property-read \App\Models\Cooperative $cooperative
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost pinned()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereCommentsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereCooperativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereIsPinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost wherePinnedUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost wherePostType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereViewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativePost whereVisibility($value)
 */
	class CooperativePost extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $cooperative_id
 * @property int $user_id
 * @property string|null $role
 * @property \Illuminate\Support\Carbon|null $joined_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cooperative $cooperative
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereCooperativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperativeUserMember whereUserId($value)
 */
	class CooperativeUserMember extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Country
 * 
 * Represents a country.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $iso_alpha2
 * @property string $iso_alpha3
 * @property string $iso_numeric
 * @property string|null $demonym
 * @property string|null $official_language
 * @property string|null $currency_code
 * @property string|null $phone_code
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $flag_url
 * @property int|null $population
 * @property float|null $gdp_usd
 * @property string|null $region_group
 * @property float|null $area_km2
 * @property float|null $altitude_m
 * @property int|null $timezone_id
 * @property-read Timezone $timezone
 * @property-read \Illuminate\Database\Eloquent\Collection|Language[] $languages
 * @property-read \Illuminate\Database\Eloquent\Collection|Region[] $regions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int|null $languages_count
 * @property-read int|null $regions_count
 * @method static \Database\Factories\CountryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereAltitudeM($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereAreaKm2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereDemonym($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereFlagUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereGdpUsd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIsoAlpha2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIsoAlpha3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIsoNumeric($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereOfficialLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country wherePhoneCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country wherePopulation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereRegionGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereUpdatedAt($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $iso_code
 * @property string $symbol
 * @property string $name
 * @property int $is_crypto
 * @property int $is_supported_by_app
 * @property int $exchangeable_in_calculator
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereExchangeableInCalculator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereIsCrypto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereIsSupportedByApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereIsoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereUpdatedAt($value)
 */
	class Currency extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string|null $url
 * @property string|null $license
 * @property string|null $last_scraped_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Stat> $stats
 * @property-read int|null $stats_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereLastScrapedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereLicense($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSource whereUrl($value)
 */
	class DataSource extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $energy_company_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $price_fixed_eur_month
 * @property string|null $price_variable_eur_kwh
 * @property int|null $price_unit_id
 * @property string $offer_type
 * @property \Illuminate\Support\Carbon|null $valid_from
 * @property \Illuminate\Support\Carbon|null $valid_until
 * @property string|null $conditions_url
 * @property int|null $contract_length_months
 * @property bool $requires_smart_meter
 * @property bool $renewable_origin_certified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EnergyCompany $energyCompany
 * @property-read \App\Models\PriceUnit|null $priceUnit
 * @method static \Database\Factories\ElectricityOfferFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereConditionsUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereContractLengthMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereEnergyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereOfferType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer wherePriceFixedEurMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer wherePriceUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer wherePriceVariableEurKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereRenewableOriginCertified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereRequiresSmartMeter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereValidFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityOffer whereValidUntil($value)
 */
	class ElectricityOffer extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $date
 * @property int|null $hour
 * @property string $type
 * @property string $price_eur_mwh
 * @property string|null $price_min
 * @property string|null $price_max
 * @property string|null $price_avg
 * @property bool $forecast_for_tomorrow
 * @property string|null $source
 * @property int|null $price_unit_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ElectricityPriceInterval> $intervals
 * @property-read int|null $intervals_count
 * @property-read \App\Models\PriceUnit|null $priceUnit
 * @method static \Database\Factories\ElectricityPriceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereForecastForTomorrow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice wherePriceAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice wherePriceEurMwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice wherePriceMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice wherePriceMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice wherePriceUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPrice whereUpdatedAt($value)
 */
	class ElectricityPrice extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $electricity_price_id
 * @property int $interval_index
 * @property string $start_time
 * @property string $end_time
 * @property string $price_eur_mwh
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ElectricityPrice $electricityPrice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereElectricityPriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereIntervalIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval wherePriceEurMwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElectricityPriceInterval whereUpdatedAt($value)
 */
	class ElectricityPriceInterval extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $activity
 * @property numeric $factor_kg_co2e_per_unit
 * @property string $unit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $activity_category
 * @property-read string $category_color
 * @property-read float $factor_tonnes_co2e_per_unit
 * @property-read string $formatted_factor
 * @property-read string $formatted_factor_tonnes
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor whereActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor whereFactorKgCo2ePerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmissionFactor whereUpdatedAt($value)
 */
	class EmissionFactor extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $building_type
 * @property string $energy_rating
 * @property numeric $annual_energy_consumption_kwh
 * @property numeric $annual_emissions_kg_co2e
 * @property int|null $zone_climate_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $efficiency_category
 * @property-read mixed $efficiency_color
 * @property-read mixed $formatted_consumption
 * @property-read mixed $formatted_emissions
 * @property-read \App\Models\User $user
 * @property-read \App\Models\ZoneClimate|null $zoneClimate
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate byBuildingType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate byEnergyRating($rating)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate highEfficiency()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate lowEfficiency()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate mediumEfficiency()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereAnnualEmissionsKgCo2e($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereAnnualEnergyConsumptionKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereBuildingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereEnergyRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCertificate whereZoneClimateId($value)
 */
	class EnergyCertificate extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $website
 * @property string|null $phone_customer
 * @property string|null $phone_commercial
 * @property string|null $email_customer
 * @property string|null $email_commercial
 * @property string|null $highlighted_offer
 * @property string|null $cnmc_id
 * @property string|null $logo_url
 * @property int|null $image_id
 * @property string $company_type
 * @property string|null $address
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string $coverage_scope
 * @property int|null $municipality_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ElectricityOffer> $electricityOffers
 * @property-read int|null $electricity_offers_count
 * @property-read \App\Models\Image|null $image
 * @property-read \App\Models\Municipality|null $municipality
 * @method static \Database\Factories\EnergyCompanyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereCnmcId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereCompanyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereCoverageScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereEmailCommercial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereEmailCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereHighlightedOffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany wherePhoneCommercial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany wherePhoneCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyCompany whereWebsite($value)
 */
	class EnergyCompany extends \Eloquent {}
}

namespace App\Models{
/**
 * Instalación energética para autoconsumo.
 * 
 * Representa una instalación de generación energética como placas solares,
 * aerogeneradores, sistemas de biomasa, etc. para autoconsumo residencial o industrial.
 *
 * @property int $id
 * @property string $name Nombre de la instalación
 * @property string $type Tipo de instalación (solar, wind, hydro, biomass, other)
 * @property float $capacity_kw Capacidad en kilovatios
 * @property string $location Ubicación de la instalación
 * @property int|null $owner_id ID del propietario (usuario)
 * @property \Carbon\Carbon|null $commissioned_at Fecha de puesta en marcha
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User|null $owner Propietario de la instalación
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EnergyTransaction[] $energyTransactions Transacciones energéticas
 * @property-read int|null $energy_transactions_count
 * @property-read mixed $estimated_monthly_production
 * @property-read mixed $status
 * @property-read mixed $type_name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation commissioned()
 * @method static \Database\Factories\EnergyInstallationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation inDevelopment()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation maxCapacity($capacity)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation minCapacity($capacity)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation ofType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereCapacityKw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereCommissionedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyInstallation whereUpdatedAt($value)
 */
	class EnergyInstallation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $producer_id
 * @property int $consumer_id
 * @property int $installation_id
 * @property float $amount_kwh
 * @property float $price_per_kwh
 * @property \Illuminate\Support\Carbon $transaction_datetime
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $consumer
 * @property-read \App\Models\EnergyInstallation $installation
 * @property-read \App\Models\User $producer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereAmountKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereConsumerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereInstallationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction wherePricePerKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereProducerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereTransactionDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyTransaction whereUpdatedAt($value)
 */
	class EnergyTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $start_datetime
 * @property \Illuminate\Support\Carbon|null $end_datetime
 * @property int|null $venue_id
 * @property int|null $event_type_id
 * @property int|null $festival_id
 * @property int|null $language_id
 * @property int|null $timezone_id
 * @property int|null $municipality_id
 * @property int|null $point_of_interest_id
 * @property int|null $work_id
 * @property numeric|null $price
 * @property bool $is_free
 * @property int|null $audience_size_estimate
 * @property string|null $source_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $artists
 * @property-read int|null $artists_count
 * @property-read \App\Models\EventType|null $eventType
 * @property-read \App\Models\Festival|null $festival
 * @property-read \App\Models\Language|null $language
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \App\Models\PointOfInterest|null $pointOfInterest
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\Timezone|null $timezone
 * @property-read \App\Models\Venue|null $venue
 * @property-read \App\Models\Work|null $work
 * @method static \Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereAudienceSizeEstimate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEndDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereFestivalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereIsFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event wherePointOfInterestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereSourceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereStartDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereVenueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereWorkId($value)
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @method static \Database\Factories\EventTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventType whereUpdatedAt($value)
 */
	class EventType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $from_currency
 * @property string $to_currency
 * @property string $rate
 * @property \Illuminate\Support\Carbon $date
 * @property string $source
 * @property string $market_type
 * @property int|null $precision
 * @property string|null $unit
 * @property string|null $volume_usd
 * @property string|null $market_cap
 * @property \Illuminate\Support\Carbon|null $retrieved_at
 * @property bool $is_active
 * @property bool $is_promoted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\ExchangeRateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereFromCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereIsPromoted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereMarketCap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereMarketType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate wherePrecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereRetrievedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereToCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExchangeRate whereVolumeUsd($value)
 */
	class ExchangeRate extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $expertise_area
 * @property string $verification_level
 * @property string $status
 * @property array<array-key, mixed>|null $credentials
 * @property array<array-key, mixed>|null $verification_documents
 * @property string $expertise_description
 * @property int $years_experience
 * @property array<array-key, mixed>|null $certifications
 * @property array<array-key, mixed>|null $education
 * @property array<array-key, mixed>|null $work_history
 * @property string $verification_fee
 * @property int|null $verified_by
 * @property \Illuminate\Support\Carbon $submitted_at
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property string|null $verification_notes
 * @property string|null $rejection_reason
 * @property int|null $verification_score
 * @property bool $is_public
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\User|null $verifier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification valid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereCertifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereCredentials($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereEducation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereExpertiseArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereExpertiseDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereVerificationDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereVerificationFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereVerificationLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereVerificationNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereVerificationScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereVerifiedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereWorkHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpertVerification whereYearsExperience($value)
 */
	class ExpertVerification extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $person_id
 * @property int $relative_id
 * @property string $relationship_type
 * @property bool $is_biological
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\Person $relative
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember whereIsBiological($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember whereRelationshipType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember whereRelativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FamilyMember whereUpdatedAt($value)
 */
	class FamilyMember extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int|null $month
 * @property string|null $usual_days
 * @property int $recurring
 * @property int|null $location_id
 * @property string|null $logo_url
 * @property string|null $color_theme
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \App\Models\Municipality|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Database\Factories\FestivalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereColorTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereRecurring($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Festival whereUsualDays($value)
 */
	class Festival extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $family
 * @property string $style
 * @property int|null $weight
 * @property string|null $license
 * @property string $source_url
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $artists
 * @property-read int|null $artists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cooperative> $cooperatives
 * @property-read int|null $cooperatives_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Festival> $festivals
 * @property-read int|null $festivals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VisualIdentity> $visualIdentities
 * @property-read int|null $visual_identities_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereFamily($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereLicense($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereSourceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Font whereWeight($value)
 */
	class Font extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $artists
 * @property-read int|null $artists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $members
 * @property-read int|null $members_count
 * @method static \Database\Factories\GroupFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereUpdatedAt($value)
 */
	class Group extends \Eloquent {}
}

namespace App\Models{
/**
 * Sistema de hashtags inteligente con trending y categorización.
 * 
 * Gestiona hashtags con auto-categorización, trending automático
 * y sugerencias inteligentes basadas en contenido.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $color
 * @property string|null $icon
 * @property string $category
 * @property int $usage_count
 * @property int $posts_count
 * @property int $followers_count
 * @property numeric $trending_score
 * @property bool $is_trending
 * @property bool $is_verified
 * @property bool $is_blocked
 * @property int|null $created_by
 * @property array<array-key, mixed>|null $related_hashtags
 * @property array<array-key, mixed>|null $synonyms
 * @property bool $auto_suggest
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContentHashtag> $contentHashtags
 * @property-read int|null $content_hashtags_count
 * @property-read \App\Models\User|null $creator
 * @method static \Database\Factories\HashtagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereAutoSuggest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereFollowersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereIsTrending($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag wherePostsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereRelatedHashtags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereSynonyms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereTrendingScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hashtag whereUsageCount($value)
 */
	class Hashtag extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $slug
 * @property string $url
 * @property string|null $alt_text
 * @property string|null $source
 * @property int|null $width
 * @property int|null $height
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $imageable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereAltText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereWidth($value)
 */
	class Image extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $language
 * @property string $slug
 * @property string|null $native_name
 * @property string|null $iso_639_1
 * @property string|null $iso_639_2
 * @property int $rtl
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Country> $countries
 * @property-read int|null $countries_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereIso6391($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereIso6392($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereNativeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereRtl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereUpdatedAt($value)
 */
	class Language extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $period
 * @property string $scope
 * @property int|null $scope_id
 * @property array<array-key, mixed> $criteria
 * @property array<array-key, mixed>|null $rules
 * @property bool $is_active
 * @property bool $is_public
 * @property int $max_positions
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $last_calculated_at
 * @property array<array-key, mixed>|null $current_rankings
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard byPeriod(string $period)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard current()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard entity()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereCriteria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereCurrentRankings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereLastCalculatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereMaxPositions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereScopeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leaderboard whereUpdatedAt($value)
 */
	class Leaderboard extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $url
 * @property string|null $label
 * @property string $related_type
 * @property int $related_id
 * @property string $type
 * @property bool $is_primary
 * @property bool $opens_in_new_tab
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $related
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereOpensInNewTab($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereRelatedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Link whereUrl($value)
 */
	class Link extends \Eloquent {}
}

namespace App\Models{
/**
 * Elemento individual dentro de una lista de usuario.
 * 
 * Representa un elemento específico (post, usuario, proyecto, etc.)
 * dentro de una lista con metadata personalizada.
 *
 * @property int $id
 * @property int $user_list_id
 * @property string $listable_type
 * @property int $listable_id
 * @property int $added_by
 * @property int $position
 * @property string|null $personal_note
 * @property array<array-key, mixed>|null $tags
 * @property numeric|null $personal_rating
 * @property string $added_mode
 * @property string $status
 * @property int|null $reviewed_by
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property int $clicks_count
 * @property int $likes_count
 * @property \Illuminate\Support\Carbon|null $last_accessed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $addedBy
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $listable
 * @property-read \App\Models\User|null $reviewedBy
 * @property-read \App\Models\UserList $userList
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereAddedMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereClicksCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereLastAccessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereListableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereListableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem wherePersonalNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem wherePersonalRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListItem whereUserListId($value)
 */
	class ListItem extends \Eloquent {}
}

namespace App\Models{
/**
 * Contactos de medios de comunicación para gestión de relaciones públicas.
 * 
 * Sistema especializado para gestionar contactos de prensa, editores,
 * corresponsales y otros profesionales de medios de comunicación
 * con funcionalidades avanzadas para campañas de comunicación.
 *
 * @property int $id
 * @property int $media_outlet_id ID del medio de comunicación
 * @property string $type Tipo de contacto
 * @property string $contact_name Nombre del contacto
 * @property string|null $job_title Cargo/posición
 * @property string|null $department Departamento
 * @property string|null $phone Teléfono principal
 * @property string|null $mobile_phone Teléfono móvil
 * @property string|null $email Email principal
 * @property string|null $secondary_email Email secundario
 * @property array|null $specializations Especializaciones temáticas
 * @property array|null $coverage_areas Áreas de cobertura
 * @property string|null $preferred_contact_method Método contacto preferido
 * @property array|null $availability_schedule Horario disponibilidad
 * @property string|null $language_preference Idioma preferido
 * @property bool $accepts_press_releases Si acepta comunicados
 * @property bool $accepts_interviews Si acepta entrevistas
 * @property bool $accepts_events_invitations Si acepta invitaciones eventos
 * @property bool $is_freelancer Si es freelance
 * @property bool $is_active Contacto activo
 * @property bool $is_verified Contacto verificado
 * @property int $priority_level Nivel de prioridad (1-5)
 * @property float|null $response_rate Tasa de respuesta
 * @property int $contacts_count Número de contactos realizados
 * @property int $successful_contacts Contactos exitosos
 * @property array|null $social_media_profiles Perfiles redes sociales
 * @property string|null $bio Biografía breve
 * @property array|null $recent_articles Artículos recientes
 * @property string|null $notes Notas internas
 * @property array|null $interaction_history Historial interacciones
 * @property \Carbon\Carbon|null $last_contacted_at Último contacto
 * @property \Carbon\Carbon|null $last_response_at Última respuesta
 * @property \Carbon\Carbon|null $verified_at Fecha verificación
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\MediaOutlet $mediaOutlet
 * @property-read mixed $contact_info
 * @property-read mixed $interaction_metrics
 * @property-read mixed $priority_level_name
 * @property-read mixed $professional_profile
 * @property-read mixed $type_name
 * @property-read mixed $verification_status
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact byPriority($priority)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact correspondents()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact editorial()
 * @method static \Database\Factories\MediaContactFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact freelancers()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact highPriority()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact pressContacts()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact responsive($minRate = '0.7')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact sustainabilityFocused()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact verified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereAcceptsEventsInvitations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereAcceptsInterviews($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereAcceptsPressReleases($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereAvailabilitySchedule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereContactsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereCoverageAreas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereInteractionHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereIsFreelancer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereJobTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereLanguagePreference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereLastContactedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereLastResponseAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereMediaOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereMobilePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact wherePreferredContactMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact wherePriorityLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereRecentArticles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereResponseRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereSecondaryEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereSocialMediaProfiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereSpecializations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereSuccessfulContacts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaContact whereVerifiedAt($value)
 */
	class MediaContact extends \Eloquent {}
}

namespace App\Models{
/**
 * Medios de comunicación para gestión de fuentes mediáticas.
 * 
 * Sistema completo de gestión de medios de comunicación con
 * clasificación por tipo, alcance, especialización temática
 * y métricas de influencia y credibilidad.
 *
 * @property int $id
 * @property string $name Nombre del medio
 * @property string $slug Slug único para URL
 * @property string $type Tipo de medio
 * @property string $media_category Categoría mediática
 * @property string|null $description Descripción del medio
 * @property string|null $website URL del sitio web
 * @property string|null $rss_feed URL del feed RSS
 * @property string|null $headquarters_location Ubicación sede
 * @property int|null $municipality_id Municipio sede
 * @property string|null $coverage_scope Alcance de cobertura
 * @property array|null $languages Idiomas de publicación
 * @property int|null $circulation Tirada/audiencia
 * @property string|null $circulation_type Tipo de circulación
 * @property int|null $founding_year Año de fundación
 * @property string|null $owner_company Empresa propietaria
 * @property string|null $political_leaning Orientación política
 * @property array|null $specializations Especializaciones temáticas
 * @property bool $is_digital_native Si es nativo digital
 * @property bool $is_verified Medio verificado
 * @property bool $is_active Medio activo
 * @property bool $covers_sustainability Si cubre sostenibilidad
 * @property float|null $credibility_score Puntuación credibilidad
 * @property float|null $influence_score Puntuación influencia
 * @property float|null $sustainability_focus Enfoque sostenibilidad
 * @property int $articles_count Número de artículos
 * @property int $monthly_pageviews Visitas mensuales
 * @property int $social_media_followers Seguidores redes sociales
 * @property array|null $social_media_handles Cuentas redes sociales
 * @property string|null $contact_email Email de contacto
 * @property string|null $press_contact_name Nombre contacto prensa
 * @property string|null $press_contact_email Email contacto prensa
 * @property string|null $press_contact_phone Teléfono contacto prensa
 * @property array|null $editorial_team Equipo editorial
 * @property string|null $content_licensing Licencias contenido
 * @property bool $allows_reprints Si permite reimpresiones
 * @property array|null $api_access Acceso a API
 * @property string|null $logo_url URL del logo
 * @property \Carbon\Carbon|null $last_scraped_at Último scraping
 * @property \Carbon\Carbon|null $verified_at Fecha verificación
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MediaContact[] $contacts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NewsArticle[] $newsArticles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $specializedTags
 * @property string|null $language
 * @property-read int|null $contacts_count
 * @property-read mixed $audience_profile
 * @property-read mixed $coverage_scope_name
 * @property-read mixed $is_reference_media
 * @property-read mixed $primary_press_contact
 * @property-read mixed $quality_metrics
 * @property-read mixed $specialization_info
 * @property-read mixed $type_name
 * @property-read int|null $news_articles_count
 * @property-read int|null $specialized_tags_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet digitalNative()
 * @method static \Database\Factories\MediaOutletFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet highCredibility($minScore = '7')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet influential($minScore = '7')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet local()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet national()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet sustainabilityFocused()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet verified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereAllowsReprints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereApiAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereArticlesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereCirculation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereCirculationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereContentLicensing($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereCoverageScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereCoversSustainability($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereCredibilityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereEditorialTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereFoundingYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereHeadquartersLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereInfluenceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereIsDigitalNative($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereLanguages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereLastScrapedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereMediaCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereMonthlyPageviews($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereOwnerCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet wherePoliticalLeaning($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet wherePressContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet wherePressContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet wherePressContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereRssFeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereSocialMediaFollowers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereSocialMediaHandles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereSpecializations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereSustainabilityFocus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaOutlet whereWebsite($value)
 */
	class MediaOutlet extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $ine_code
 * @property string|null $postal_code
 * @property int|null $population
 * @property string|null $mayor_name
 * @property string|null $mayor_salary
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $area_km2
 * @property int|null $altitude_m
 * @property int $is_capital
 * @property string|null $tourism_info
 * @property int|null $region_id
 * @property int $province_id
 * @property int $autonomous_community_id
 * @property int $country_id
 * @property int|null $timezone_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AutonomousCommunity $autonomousCommunity
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PointOfInterest> $pointsOfInterest
 * @property-read int|null $points_of_interest_count
 * @property-read \App\Models\Province $province
 * @property-read \App\Models\Region|null $region
 * @property-read \App\Models\Timezone|null $timezone
 * @method static \Database\Factories\MunicipalityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereAltitudeM($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereAreaKm2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereAutonomousCommunityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereIneCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereIsCapital($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereMayorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereMayorSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality wherePopulation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereTourismInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereUpdatedAt($value)
 */
	class Municipality extends \Eloquent {}
}

namespace App\Models{
/**
 * Artículos de noticias para gestión de contenido mediático.
 * 
 * Sistema completo de gestión de noticias con soporte para múltiples
 * idiomas, categorización, geolocalización y análisis de engagement.
 * Incluye funcionalidades avanzadas para medios sostenibles y ambientales.
 *
 * @property int $id
 * @property string $title Título del artículo
 * @property string $slug Slug único para URL
 * @property string|null $summary Resumen/entradilla del artículo
 * @property string $content Contenido completo del artículo
 * @property string|null $excerpt Extracto automático
 * @property string|null $source_url URL original del artículo
 * @property string|null $original_title Título original (si traducido)
 * @property \Carbon\Carbon|null $published_at Fecha de publicación
 * @property \Carbon\Carbon|null $featured_start Inicio destacado
 * @property \Carbon\Carbon|null $featured_end Fin destacado
 * @property int|null $media_outlet_id Medio de comunicación
 * @property int|null $author_id Autor del artículo
 * @property int|null $municipality_id Municipio relacionado
 * @property int|null $language_id Idioma del artículo
 * @property int|null $image_id Imagen principal
 * @property string $category Categoría del artículo
 * @property string $topic_focus Enfoque temático específico
 * @property string $article_type Tipo de artículo
 * @property bool $is_outstanding Artículo destacado
 * @property bool $is_verified Artículo verificado
 * @property bool $is_scraped Obtenido por scraping
 * @property bool $is_translated Artículo traducido
 * @property bool $is_breaking_news Noticia de última hora
 * @property bool $is_evergreen Contenido perenne
 * @property string $visibility Visibilidad del artículo
 * @property string $status Estado del artículo
 * @property int $views_count Número de visualizaciones
 * @property int $shares_count Número de compartidos
 * @property int $comments_count Número de comentarios
 * @property float|null $reading_time_minutes Tiempo estimado lectura
 * @property int|null $word_count Número de palabras
 * @property float|null $sentiment_score Puntuación de sentimiento
 * @property string|null $sentiment_label Etiqueta de sentimiento
 * @property array|null $keywords Palabras clave extraídas
 * @property array|null $entities Entidades nombradas
 * @property array|null $sustainability_topics Temas de sostenibilidad
 * @property float|null $environmental_impact_score Puntuación impacto ambiental
 * @property array|null $related_co2_data Datos CO2 relacionados
 * @property string|null $geo_scope Alcance geográfico
 * @property float|null $latitude Latitud del contenido
 * @property float|null $longitude Longitud del contenido
 * @property string|null $seo_title Título SEO
 * @property string|null $seo_description Descripción SEO
 * @property array|null $social_media_meta Metadatos redes sociales
 * @property \Carbon\Carbon|null $scraped_at Fecha de scraping
 * @property \Carbon\Carbon|null $last_engagement_at Último engagement
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\MediaOutlet|null $mediaOutlet
 * @property-read \App\Models\Person|null $author
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \App\Models\Language|null $language
 * @property-read \App\Models\Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserGeneratedContent[] $userComments
 * @property int|null $tag_id
 * @property-read mixed $engagement_rate
 * @property-read mixed $is_currently_featured
 * @property-read mixed $sentiment_level
 * @property-read mixed $social_share_data
 * @property-read mixed $url
 * @property-read int|null $tags_count
 * @property-read int|null $user_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserGeneratedContent> $userContent
 * @property-read int|null $user_content_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle breaking()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle byCategory($category)
 * @method static \Database\Factories\NewsArticleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle highEngagement($minViews = 1000)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle nearLocation($lat, $lng, $radiusKm = 50)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle search($search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle sustainability()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereArticleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereEntities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereEnvironmentalImpactScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereFeaturedEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereFeaturedStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereGeoScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereIsBreakingNews($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereIsEvergreen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereIsOutstanding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereIsScraped($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereIsTranslated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereLastEngagementAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereMediaOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereOriginalTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereReadingTimeMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereRelatedCo2Data($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereScrapedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSentimentLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSentimentScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSeoDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSharesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSocialMediaMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSourceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSustainabilityTopics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereTopicFocus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereViewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereWordCount($value)
 */
	class NewsArticle extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property int|null $target_id
 * @property numeric|null $threshold
 * @property string $delivery_method
 * @property bool $is_silent
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereDeliveryMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereIsSilent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereUserId($value)
 */
	class NotificationSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $logo
 * @property string|null $domain
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property string|null $primary_color
 * @property string|null $secondary_color
 * @property array<array-key, mixed>|null $css_files
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AppSetting|null $appSettings
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrganizationFeature> $features
 * @property-read int|null $features_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCssFiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereUpdatedAt($value)
 */
	class Organization extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $organization_id
 * @property string $feature_key
 * @property bool $enabled_dashboard
 * @property bool $enabled_web
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Organization $organization
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereEnabledDashboard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereEnabledWeb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereFeatureKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationFeature whereUpdatedAt($value)
 */
	class OrganizationFeature extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $payable_type
 * @property int $payable_id
 * @property string $payment_intent_id
 * @property string $status
 * @property string $type
 * @property numeric $amount
 * @property numeric $fee
 * @property numeric $net_amount
 * @property string $currency
 * @property string $payment_method
 * @property string $processor
 * @property array<array-key, mixed>|null $processor_response
 * @property array<array-key, mixed>|null $metadata
 * @property string $description
 * @property string|null $failure_reason
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon|null $failed_at
 * @property \Illuminate\Support\Carbon|null $refunded_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $payable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment byProcessor(string $processor)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment failed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment processing()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment refunded()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereNetAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePayableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePayableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentIntentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereProcessor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereProcessorResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereRefundedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUserId($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Person
 * 
 * Represents a person with biographical and relational data.
 *
 * @property int $id
 * @property string $name
 * @property string|null $birth_name
 * @property string $slug
 * @property \Carbon\Carbon|null $birth_date
 * @property \Carbon\Carbon|null $death_date
 * @property string|null $birth_place
 * @property string|null $death_place
 * @property int|null $nationality_id
 * @property int|null $language_id
 * @property int|null $image_id
 * @property string|null $gender
 * @property string|null $official_website
 * @property string|null $wikidata_id
 * @property string|null $wikipedia_url
 * @property string|null $notable_for
 * @property string|null $occupation_summary
 * @property array|null $social_handles
 * @property bool $is_influencer
 * @property int|null $search_boost
 * @property string|null $short_bio
 * @property string|null $long_bio
 * @property string|null $source_url
 * @property \Carbon\Carbon|null $last_updated_from_source
 * @property-read Country $nationality
 * @property-read Language $language
 * @property-read Image $image
 * @property-read \Illuminate\Database\Eloquent\Collection|Alias[] $aliases
 * @property-read \Illuminate\Database\Eloquent\Collection|Tag[] $tags
 * @property-read Appearance|null $appearance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int|null $aliases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AwardWinner> $awards
 * @property-read int|null $awards_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FamilyMember> $familyMembers
 * @property-read int|null $family_members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Work> $leadingWorks
 * @property-read int|null $leading_works_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Link> $links
 * @property-read int|null $links_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Profession> $primaryProfession
 * @property-read int|null $primary_profession_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Profession> $professions
 * @property-read int|null $professions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialAccount> $socialAccounts
 * @property-read int|null $social_accounts_count
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Work> $works
 * @property-read int|null $works_count
 * @method static \Database\Factories\PersonFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereBirthName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereBirthPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereDeathDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereDeathPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereIsInfluencer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereLastUpdatedFromSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereLongBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereNationalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereNotableFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereOccupationSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereOfficialWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereSearchBoost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereShortBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereSocialHandles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereSourceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereWikidataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereWikipediaUrl($value)
 */
	class Person extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $person_id
 * @property int $profession_id
 * @property int|null $start_year
 * @property int|null $end_year
 * @property bool $is_primary
 * @property bool $is_current
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\Profession $profession
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonProfession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonProfession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonProfession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonProfession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonProfession whereEndYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonProfession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonProfession whereIsCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonProfession whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonProfession whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonProfession wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonProfession whereProfessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonProfession whereStartYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonProfession whereUpdatedAt($value)
 */
	class PersonProfession extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $person_id
 * @property int $work_id
 * @property string $role
 * @property string|null $character_name
 * @property string|null $credited_as
 * @property int|null $billing_order
 * @property string|null $contribution_pct
 * @property int $is_primary
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork whereBillingOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork whereCharacterName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork whereContributionPct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork whereCreditedAs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonWork whereWorkId($value)
 */
	class PersonWork extends \Eloquent {}
}

namespace App\Models{
/**
 * Especies vegetales para reforestación y compensación de CO2.
 * 
 * Catálogo de plantas y árboles con información sobre su capacidad
 * de absorción de CO2, características de crecimiento, y utilidad
 * para proyectos de compensación ambiental.
 *
 * @property int $id
 * @property string $name Nombre común de la especie
 * @property string $slug Slug único para URLs
 * @property string|null $scientific_name Nombre científico
 * @property string|null $family Familia botánica
 * @property float $co2_absorption_kg_per_year CO2 absorbido por año (kg)
 * @property float|null $co2_absorption_min Absorción mínima (kg/año)
 * @property float|null $co2_absorption_max Absorción máxima (kg/año)
 * @property string|null $description Descripción de la especie
 * @property string $plant_type Tipo: tree, shrub, herb, grass, etc
 * @property string $size_category Categoría: small, medium, large, giant
 * @property float|null $height_min Altura mínima (metros)
 * @property float|null $height_max Altura máxima (metros)
 * @property int|null $lifespan_years Esperanza de vida (años)
 * @property int|null $growth_rate_cm_year Velocidad crecimiento (cm/año)
 * @property string|null $climate_zones Zonas climáticas (JSON)
 * @property string|null $soil_types Tipos de suelo preferidos
 * @property float|null $water_needs_mm_year Necesidades hídricas (mm/año)
 * @property bool $drought_resistant Resistente a sequía
 * @property bool $frost_resistant Resistente a heladas
 * @property bool $is_endemic Si es especie endémica
 * @property bool $is_invasive Si es especie invasiva
 * @property bool $suitable_for_reforestation Si es apta para reforestación
 * @property bool $suitable_for_urban Si es apta para zonas urbanas
 * @property string|null $flowering_season Época de floración
 * @property string|null $fruit_season Época de fructificación
 * @property bool $provides_food Si proporciona alimento
 * @property bool $provides_timber Si proporciona madera
 * @property bool $medicinal_use Si tiene uso medicinal
 * @property float|null $planting_cost_eur Coste de plantación (euros)
 * @property float|null $maintenance_cost_eur_year Coste mantenimiento anual
 * @property int|null $survival_rate_percent Tasa de supervivencia (%)
 * @property int|null $image_id Imagen de la especie
 * @property int|null $native_region_id Región nativa
 * @property string $source Fuente de los datos
 * @property string|null $source_url URL de la fuente
 * @property bool $is_verified Si está verificado científicamente
 * @property string|null $verification_entity Entidad verificadora
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Image|null $image
 * @property-read \App\Models\Region|null $nativeRegion
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PlantationProject[] $plantationProjects
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Municipality[] $suitableMunicipalities
 * @property-read mixed $additional_benefits
 * @property-read mixed $c_o2_absorption_color
 * @property-read mixed $c_o2_absorption_level
 * @property-read mixed $c_o2_efficiency
 * @property-read mixed $plant_type_name
 * @property-read mixed $project_recommendation
 * @property-read mixed $reforestation_score
 * @property-read mixed $size_category_name
 * @property-read int|null $suitable_municipalities_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies droughtResistant()
 * @method static \Database\Factories\PlantSpeciesFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies fastGrowing($minCmYear = 50)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies forReforestation()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies forUrban()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies highCO2Absorption($minKg = 20)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies native($regionId = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies trees()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies verified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereClimateZones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereCo2AbsorptionKgPerYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereCo2AbsorptionMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereCo2AbsorptionMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereDroughtResistant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereFamily($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereFloweringSeason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereFrostResistant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereFruitSeason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereGrowthRateCmYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereHeightMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereHeightMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereIsEndemic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereIsInvasive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereLifespanYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereMaintenanceCostEurYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereMedicinalUse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereNativeRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies wherePlantType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies wherePlantingCostEur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereProvidesFood($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereProvidesTimber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereScientificName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereSizeCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereSoilTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereSourceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereSuitableForReforestation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereSuitableForUrban($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereSurvivalRatePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereVerificationEntity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantSpecies whereWaterNeedsMmYear($value)
 */
	class PlantSpecies extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $url_pattern
 * @property string $type
 * @property string|null $icon
 * @property string|null $color
 * @property string|null $description
 * @property int $is_active
 * @property int $requires_verification
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PlatformFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform whereRequiresVerification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform whereUrlPattern($value)
 */
	class Platform extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $address
 * @property string|null $type
 * @property string|null $latitude
 * @property string|null $longitude
 * @property int $municipality_id
 * @property string|null $source
 * @property string|null $description
 * @property bool $is_cultural_center
 * @property bool $is_energy_installation
 * @property bool $is_cooperative_office
 * @property array<array-key, mixed>|null $opening_hours
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Municipality $municipality
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereIsCooperativeOffice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereIsCulturalCenter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereIsEnergyInstallation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereOpeningHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointOfInterest whereUpdatedAt($value)
 */
	class PointOfInterest extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string|null $unit_code
 * @property string|null $conversion_factor_to_kwh
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereConversionFactorToKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereUnitCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceUnit whereUpdatedAt($value)
 */
	class PriceUnit extends \Eloquent {}
}

namespace App\Models{
/**
 * Sistema de derechos de producción energética.
 * 
 * Permite la compra/venta de derechos sobre la producción
 * energética de instalaciones solares.
 *
 * @property int $id
 * @property int $seller_id
 * @property int|null $buyer_id
 * @property int|null $installation_id
 * @property int|null $project_proposal_id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string $right_identifier
 * @property string $right_type
 * @property numeric $total_capacity_kw
 * @property numeric $available_capacity_kw
 * @property numeric $reserved_capacity_kw
 * @property numeric $sold_capacity_kw
 * @property numeric|null $estimated_annual_production_kwh
 * @property numeric|null $guaranteed_annual_production_kwh
 * @property numeric $actual_annual_production_kwh
 * @property \Illuminate\Support\Carbon $valid_from
 * @property \Illuminate\Support\Carbon $valid_until
 * @property int|null $duration_years
 * @property bool $renewable_right
 * @property int|null $renewal_period_years
 * @property string $pricing_model
 * @property numeric|null $price_per_kwh
 * @property numeric|null $market_premium_percentage
 * @property numeric|null $minimum_guaranteed_price
 * @property numeric|null $maximum_price_cap
 * @property array<array-key, mixed>|null $price_escalation_terms
 * @property numeric|null $upfront_payment
 * @property numeric|null $periodic_payment
 * @property string|null $payment_frequency
 * @property numeric|null $security_deposit
 * @property array<array-key, mixed>|null $payment_terms
 * @property array<array-key, mixed>|null $penalty_clauses
 * @property bool $production_guaranteed
 * @property numeric|null $production_guarantee_percentage
 * @property bool $insurance_included
 * @property string|null $insurance_details
 * @property array<array-key, mixed>|null $risk_allocation
 * @property array<array-key, mixed>|null $buyer_rights
 * @property array<array-key, mixed>|null $buyer_obligations
 * @property array<array-key, mixed>|null $seller_rights
 * @property array<array-key, mixed>|null $seller_obligations
 * @property bool $is_transferable
 * @property int|null $max_transfers
 * @property int $current_transfers
 * @property array<array-key, mixed>|null $transfer_restrictions
 * @property numeric|null $transfer_fee_percentage
 * @property string $status
 * @property string|null $status_notes
 * @property \Illuminate\Support\Carbon|null $contract_signed_at
 * @property \Illuminate\Support\Carbon|null $activated_at
 * @property numeric $current_month_production_kwh
 * @property numeric $ytd_production_kwh
 * @property numeric $lifetime_production_kwh
 * @property numeric $performance_ratio
 * @property array<array-key, mixed>|null $monthly_production_history
 * @property string|null $regulatory_framework
 * @property array<array-key, mixed>|null $applicable_regulations
 * @property bool $grid_code_compliant
 * @property array<array-key, mixed>|null $certifications
 * @property array<array-key, mixed>|null $legal_documents
 * @property string|null $contract_template_version
 * @property bool $electronic_signature_valid
 * @property array<array-key, mixed>|null $signature_details
 * @property int $views_count
 * @property int $inquiries_count
 * @property int $offers_received
 * @property numeric|null $highest_offer_price
 * @property numeric|null $average_market_price
 * @property bool $is_active
 * @property bool $is_featured
 * @property bool $auto_accept_offers
 * @property numeric|null $auto_accept_threshold
 * @property bool $allow_partial_sales
 * @property numeric|null $minimum_sale_capacity_kw
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $buyer
 * @property-read \App\Models\EnergyInstallation|null $installation
 * @property-read \App\Models\ProjectProposal|null $projectProposal
 * @property-read \App\Models\User $seller
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereActivatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereActualAnnualProductionKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereAllowPartialSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereApplicableRegulations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereAutoAcceptOffers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereAutoAcceptThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereAvailableCapacityKw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereAverageMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereBuyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereBuyerObligations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereBuyerRights($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereCertifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereContractSignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereContractTemplateVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereCurrentMonthProductionKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereCurrentTransfers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereDurationYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereElectronicSignatureValid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereEstimatedAnnualProductionKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereGridCodeCompliant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereGuaranteedAnnualProductionKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereHighestOfferPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereInquiriesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereInstallationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereInsuranceDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereInsuranceIncluded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereIsTransferable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereLegalDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereLifetimeProductionKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereMarketPremiumPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereMaxTransfers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereMaximumPriceCap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereMinimumGuaranteedPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereMinimumSaleCapacityKw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereMonthlyProductionHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereOffersReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight wherePaymentFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight wherePaymentTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight wherePenaltyClauses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight wherePerformanceRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight wherePeriodicPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight wherePriceEscalationTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight wherePricePerKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight wherePricingModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereProductionGuaranteePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereProductionGuaranteed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereProjectProposalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereRegulatoryFramework($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereRenewableRight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereRenewalPeriodYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereReservedCapacityKw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereRightIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereRightType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereRiskAllocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereSecurityDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereSellerObligations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereSellerRights($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereSignatureDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereSoldCapacityKw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereStatusNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereTotalCapacityKw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereTransferFeePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereTransferRestrictions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereUpfrontPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereValidFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereValidUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereViewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRight whereYtdProductionKwh($value)
 */
	class ProductionRight extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Profession
 * 
 * Represents a profession or occupation.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $category
 * @property bool $is_public_facing
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $currentPeople
 * @property-read int|null $current_people_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $people
 * @property-read int|null $people_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $primaryPeople
 * @property-read int|null $primary_people_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereIsPublicFacing($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereUpdatedAt($value)
 */
	class Profession extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $project_proposal_id
 * @property int $user_id
 * @property string $type
 * @property numeric $amount
 * @property numeric $rate
 * @property numeric $base_amount
 * @property string $currency
 * @property string $status
 * @property \Illuminate\Support\Carbon $due_date
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string|null $payment_method
 * @property string|null $transaction_id
 * @property string $description
 * @property array<array-key, mixed> $calculation_details
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\ProjectProposal $projectProposal
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission dueSoon(int $days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission overdue()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission paid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereBaseAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereCalculationDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereProjectProposalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectCommission whereUserId($value)
 */
	class ProjectCommission extends \Eloquent {}
}

namespace App\Models{
/**
 * Inversión en un proyecto colaborativo.
 * 
 * Representa la participación de un usuario en la financiación
 * de un proyecto energético con términos específicos.
 *
 * @property int $id
 * @property int $project_proposal_id
 * @property int $investor_id
 * @property numeric $investment_amount
 * @property numeric|null $investment_percentage
 * @property string $investment_type
 * @property array<array-key, mixed>|null $investment_details
 * @property string|null $investment_description
 * @property numeric|null $expected_return_percentage
 * @property int|null $investment_term_years
 * @property string|null $return_frequency
 * @property array<array-key, mixed>|null $return_schedule
 * @property bool $reinvest_returns
 * @property string $status
 * @property string|null $payment_method
 * @property string|null $payment_reference
 * @property \Illuminate\Support\Carbon|null $payment_date
 * @property \Illuminate\Support\Carbon|null $payment_confirmed_at
 * @property int|null $payment_confirmed_by
 * @property array<array-key, mixed>|null $legal_documents
 * @property bool $terms_accepted
 * @property \Illuminate\Support\Carbon|null $terms_accepted_at
 * @property string|null $digital_signature
 * @property array<array-key, mixed>|null $contract_details
 * @property numeric $total_returns_received
 * @property numeric $pending_returns
 * @property \Illuminate\Support\Carbon|null $last_return_date
 * @property \Illuminate\Support\Carbon|null $next_return_date
 * @property bool $has_voting_rights
 * @property numeric $voting_weight
 * @property bool $can_participate_decisions
 * @property bool $receives_project_updates
 * @property array<array-key, mixed>|null $notification_preferences
 * @property bool $public_investor
 * @property string|null $investor_alias
 * @property numeric $current_roi
 * @property numeric|null $projected_final_roi
 * @property int $months_invested
 * @property array<array-key, mixed>|null $performance_metrics
 * @property bool $exit_requested
 * @property \Illuminate\Support\Carbon|null $exit_requested_at
 * @property numeric|null $exit_value
 * @property string|null $exit_terms
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $investor
 * @property-read \App\Models\User|null $paymentConfirmedBy
 * @property-read \App\Models\ProjectProposal $projectProposal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereCanParticipateDecisions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereContractDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereCurrentRoi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereDigitalSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereExitRequested($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereExitRequestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereExitTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereExitValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereExpectedReturnPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereHasVotingRights($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereInvestmentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereInvestmentDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereInvestmentDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereInvestmentPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereInvestmentTermYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereInvestmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereInvestorAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereInvestorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereLastReturnDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereLegalDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereMonthsInvested($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereNextReturnDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereNotificationPreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment wherePaymentConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment wherePaymentConfirmedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment wherePaymentReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment wherePendingReturns($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment wherePerformanceMetrics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereProjectProposalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereProjectedFinalRoi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment wherePublicInvestor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereReceivesProjectUpdates($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereReinvestReturns($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereReturnFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereReturnSchedule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereTermsAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereTermsAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereTotalReturnsReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectInvestment whereVotingWeight($value)
 */
	class ProjectInvestment extends \Eloquent {}
}

namespace App\Models{
/**
 * Sistema de propuestas de proyectos colaborativos.
 * 
 * Permite a los usuarios proponer proyectos energéticos,
 * buscar financiación colaborativa y gestionar su ejecución.
 *
 * @property int $id
 * @property int $proposer_id
 * @property int|null $cooperative_id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string|null $summary
 * @property array<array-key, mixed>|null $objectives
 * @property array<array-key, mixed>|null $benefits
 * @property string $project_type
 * @property string $scale
 * @property int|null $municipality_id
 * @property string|null $specific_location
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property numeric|null $estimated_power_kw
 * @property numeric|null $estimated_annual_production_kwh
 * @property array<array-key, mixed>|null $technical_specifications
 * @property numeric $total_investment_required
 * @property numeric $investment_raised
 * @property numeric|null $min_investment_per_participant
 * @property numeric|null $max_investment_per_participant
 * @property int|null $max_participants
 * @property int $current_participants
 * @property numeric|null $estimated_roi_percentage
 * @property int|null $payback_period_years
 * @property numeric|null $estimated_annual_savings
 * @property array<array-key, mixed>|null $financial_projections
 * @property \Illuminate\Support\Carbon $funding_deadline
 * @property \Illuminate\Support\Carbon|null $project_start_date
 * @property \Illuminate\Support\Carbon|null $expected_completion_date
 * @property int|null $estimated_duration_months
 * @property array<array-key, mixed>|null $project_milestones
 * @property array<array-key, mixed>|null $documents
 * @property array<array-key, mixed>|null $images
 * @property array<array-key, mixed>|null $technical_reports
 * @property bool $has_permits
 * @property array<array-key, mixed>|null $permits_status
 * @property bool $is_technically_validated
 * @property int|null $technical_validator_id
 * @property \Illuminate\Support\Carbon|null $technical_validation_date
 * @property string $status
 * @property string|null $status_notes
 * @property int|null $reviewed_by
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property int $views_count
 * @property int $likes_count
 * @property int $comments_count
 * @property int $shares_count
 * @property int $bookmarks_count
 * @property numeric $engagement_score
 * @property bool $is_public
 * @property bool $is_featured
 * @property bool $allow_comments
 * @property bool $allow_investments
 * @property bool $notify_updates
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cooperative|null $cooperative
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectInvestment> $investments
 * @property-read int|null $investments_count
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductionRight> $productionRights
 * @property-read int|null $production_rights_count
 * @property-read \App\Models\User $proposer
 * @property-read \App\Models\User|null $reviewedBy
 * @property-read \App\Models\User|null $technicalValidator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectUpdate> $updates
 * @property-read int|null $updates_count
 * @method static \Database\Factories\ProjectProposalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereAllowComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereAllowInvestments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereBenefits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereBookmarksCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereCooperativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereCurrentParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereEngagementScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereEstimatedAnnualProductionKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereEstimatedAnnualSavings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereEstimatedDurationMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereEstimatedPowerKw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereEstimatedRoiPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereExpectedCompletionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereFinancialProjections($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereFundingDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereHasPermits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereInvestmentRaised($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereIsTechnicallyValidated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereMaxInvestmentPerParticipant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereMaxParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereMinInvestmentPerParticipant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereNotifyUpdates($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereObjectives($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal wherePaybackPeriodYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal wherePermitsStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereProjectMilestones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereProjectStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereProjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereProposerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereScale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereSharesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereSpecificLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereStatusNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereTechnicalReports($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereTechnicalSpecifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereTechnicalValidationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereTechnicalValidatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereTotalInvestmentRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectProposal whereViewsCount($value)
 */
	class ProjectProposal extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $project_proposal_id
 * @property int $author_id
 * @property string $title
 * @property string $content
 * @property string|null $summary
 * @property string $update_type
 * @property numeric|null $progress_percentage
 * @property string|null $previous_progress_percentage
 * @property string|null $completed_milestones
 * @property string|null $upcoming_milestones
 * @property string|null $revised_completion_date
 * @property string|null $budget_spent
 * @property string|null $budget_remaining
 * @property string|null $additional_funding_needed
 * @property string|null $cost_breakdown
 * @property string|null $financial_notes
 * @property string|null $actual_power_installed_kw
 * @property string|null $production_to_date_kwh
 * @property string|null $performance_vs_expected
 * @property string|null $technical_metrics
 * @property string|null $technical_notes
 * @property array<array-key, mixed>|null $images
 * @property string|null $videos
 * @property string|null $documents
 * @property string|null $reports
 * @property string|null $co2_savings_kg
 * @property string|null $energy_savings_kwh
 * @property string|null $cost_savings_eur
 * @property string|null $environmental_impact
 * @property string|null $social_impact
 * @property int $notify_all_investors
 * @property string|null $investor_specific_info
 * @property int $requires_investor_action
 * @property string|null $required_action_description
 * @property string|null $action_deadline
 * @property int $views_count
 * @property int $likes_count
 * @property int $comments_count
 * @property int $shares_count
 * @property int $allow_comments
 * @property int $allow_questions
 * @property string $visibility
 * @property int $is_featured
 * @property int $is_urgent
 * @property int $send_email_notification
 * @property int $send_push_notification
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property string|null $scheduled_for
 * @property string|null $investor_satisfaction_score
 * @property int $questions_received
 * @property int $questions_answered
 * @property int $all_questions_answered
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $author
 * @property-read \App\Models\ProjectProposal $projectProposal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate milestones()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate ofType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereActionDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereActualPowerInstalledKw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereAdditionalFundingNeeded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereAllQuestionsAnswered($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereAllowComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereAllowQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereBudgetRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereBudgetSpent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereCo2SavingsKg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereCompletedMilestones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereCostBreakdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereCostSavingsEur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereEnergySavingsKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereEnvironmentalImpact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereFinancialNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereInvestorSatisfactionScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereInvestorSpecificInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereIsUrgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereNotifyAllInvestors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate wherePerformanceVsExpected($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate wherePreviousProgressPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereProductionToDateKwh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereProgressPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereProjectProposalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereQuestionsAnswered($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereQuestionsReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereReports($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereRequiredActionDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereRequiresInvestorAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereRevisedCompletionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereScheduledFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereSendEmailNotification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereSendPushNotification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereSharesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereSocialImpact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereTechnicalMetrics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereTechnicalNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereUpcomingMilestones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereUpdateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereVideos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereViewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate whereVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUpdate withProgress()
 */
	class ProjectUpdate extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $project_proposal_id
 * @property int $requested_by
 * @property int|null $verified_by
 * @property string $type
 * @property string $status
 * @property numeric $fee
 * @property string $currency
 * @property array<array-key, mixed> $verification_criteria
 * @property array<array-key, mixed> $documents_required
 * @property array<array-key, mixed>|null $documents_provided
 * @property array<array-key, mixed>|null $verification_results
 * @property string|null $verification_notes
 * @property string|null $rejection_reason
 * @property int|null $score
 * @property \Illuminate\Support\Carbon $requested_at
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $is_public
 * @property string|null $certificate_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\ProjectProposal $projectProposal
 * @property-read \App\Models\User $requester
 * @property-read \App\Models\User|null $verifier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification expiringSoon(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification inReview()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification rejected()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification requested()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereCertificateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereDocumentsProvided($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereDocumentsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereProjectProposalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereRequestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereRequestedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereVerificationCriteria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereVerificationNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereVerificationResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectVerification whereVerifiedBy($value)
 */
	class ProjectVerification extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Province
 * 
 * Represents a province.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $ine_code
 * @property int|null $autonomous_community_id
 * @property int|null $country_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $area_km2
 * @property float|null $altitude_m
 * @property int|null $timezone_id
 * @property-read AutonomousCommunity $autonomousCommunity
 * @property-read Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection|Region[] $regions
 * @property-read Timezone $timezone
 * @property-read \Illuminate\Database\Eloquent\Collection|Municipality[] $municipalities
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int|null $municipalities_count
 * @property-read int|null $regions_count
 * @method static \Database\Factories\ProvinceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereAltitudeM($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereAreaKm2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereAutonomousCommunityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereIneCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereUpdatedAt($value)
 */
	class Province extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Region
 * 
 * Represents a region within a country.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int|null $province_id
 * @property int|null $autonomous_community_id
 * @property int|null $country_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $area_km2
 * @property float|null $altitude_m
 * @property int|null $timezone_id
 * @property-read Province $province
 * @property-read AutonomousCommunity $autonomousCommunity
 * @property-read Country $country
 * @property-read Timezone $timezone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Municipality> $municipalities
 * @property-read int|null $municipalities_count
 * @method static \Database\Factories\RegionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereAltitudeM($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereAreaKm2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereAutonomousCommunityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereUpdatedAt($value)
 */
	class Region extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $reciprocal_slug
 * @property string $category
 * @property int|null $degree
 * @property int $gender_specific
 * @property string|null $description
 * @property int $is_symmetrical
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereGenderSpecific($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereIsSymmetrical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereReciprocalSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RelationshipType whereUpdatedAt($value)
 */
	class RelationshipType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $action_type
 * @property int $reputation_change
 * @property string|null $category
 * @property int|null $topic_id
 * @property string $related_type
 * @property int $related_id
 * @property int|null $triggered_by
 * @property string|null $description
 * @property array<array-key, mixed>|null $metadata
 * @property int $is_validated
 * @property int $is_reversed
 * @property int|null $reversed_by
 * @property string|null $reversed_at
 * @property string|null $reversal_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $awarder
 * @property-read ReputationTransaction|null $reversingTransaction
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction byCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction visible()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereActionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereIsReversed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereIsValidated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereRelatedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereReputationChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereReversalReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereReversedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereReversedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereTriggeredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReputationTransaction whereUserId($value)
 */
	class ReputationTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * Marketplace de techos y espacios para instalaciones solares.
 * 
 * Permite a los propietarios ofertar sus techos/espacios
 * para instalaciones solares colaborativas.
 *
 * @property int $id
 * @property int $owner_id
 * @property int|null $municipality_id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string $space_type
 * @property string $address
 * @property numeric $latitude
 * @property numeric $longitude
 * @property string|null $postal_code
 * @property string|null $access_instructions
 * @property array<array-key, mixed>|null $nearby_landmarks
 * @property numeric $total_area_m2
 * @property numeric $usable_area_m2
 * @property numeric|null $max_installable_power_kw
 * @property string|null $roof_orientation
 * @property int|null $roof_inclination_degrees
 * @property string|null $roof_material
 * @property string|null $roof_condition
 * @property int|null $roof_age_years
 * @property numeric|null $max_load_capacity_kg_m2
 * @property numeric|null $annual_solar_irradiation_kwh_m2
 * @property int|null $annual_sunny_days
 * @property array<array-key, mixed>|null $shading_analysis
 * @property bool $has_shading_issues
 * @property string|null $shading_description
 * @property string|null $access_difficulty
 * @property string|null $access_description
 * @property bool $crane_access
 * @property bool $vehicle_access
 * @property numeric|null $distance_to_electrical_panel_m
 * @property bool $has_building_permits
 * @property bool $community_approval_required
 * @property bool $community_approval_obtained
 * @property array<array-key, mixed>|null $required_permits
 * @property array<array-key, mixed>|null $obtained_permits
 * @property string|null $legal_restrictions
 * @property string $offering_type
 * @property numeric|null $monthly_rent_eur
 * @property numeric|null $sale_price_eur
 * @property numeric|null $energy_share_percentage
 * @property int|null $contract_duration_years
 * @property bool $renewable_contract
 * @property array<array-key, mixed>|null $additional_terms
 * @property bool $includes_maintenance
 * @property bool $includes_insurance
 * @property bool $includes_permits_management
 * @property bool $includes_monitoring
 * @property array<array-key, mixed>|null $included_services
 * @property array<array-key, mixed>|null $additional_costs
 * @property string $availability_status
 * @property \Illuminate\Support\Carbon|null $available_from
 * @property \Illuminate\Support\Carbon|null $available_until
 * @property string|null $availability_notes
 * @property bool $owner_lives_onsite
 * @property string $owner_involvement
 * @property array<array-key, mixed>|null $owner_preferences
 * @property string|null $owner_requirements
 * @property int $views_count
 * @property int $inquiries_count
 * @property int $bookmarks_count
 * @property numeric|null $rating
 * @property int $reviews_count
 * @property array<array-key, mixed>|null $images
 * @property array<array-key, mixed>|null $documents
 * @property array<array-key, mixed>|null $technical_reports
 * @property array<array-key, mixed>|null $solar_analysis_reports
 * @property bool $is_active
 * @property bool $is_featured
 * @property bool $is_verified
 * @property int|null $verified_by
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property bool $auto_respond_inquiries
 * @property string|null $auto_response_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \App\Models\User $owner
 * @property-read \App\Models\User|null $verifiedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAccessDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAccessDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAccessInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAdditionalCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAdditionalTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAnnualSolarIrradiationKwhM2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAnnualSunnyDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAutoRespondInquiries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAutoResponseMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAvailabilityNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAvailabilityStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAvailableFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereAvailableUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereBookmarksCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereCommunityApprovalObtained($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereCommunityApprovalRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereContractDurationYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereCraneAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereDistanceToElectricalPanelM($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereEnergySharePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereHasBuildingPermits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereHasShadingIssues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereIncludedServices($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereIncludesInsurance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereIncludesMaintenance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereIncludesMonitoring($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereIncludesPermitsManagement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereInquiriesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereLegalRestrictions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereMaxInstallablePowerKw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereMaxLoadCapacityKgM2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereMonthlyRentEur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereNearbyLandmarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereObtainedPermits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereOfferingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereOwnerInvolvement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereOwnerLivesOnsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereOwnerPreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereOwnerRequirements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereRenewableContract($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereRequiredPermits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereReviewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereRoofAgeYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereRoofCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereRoofInclinationDegrees($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereRoofMaterial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereRoofOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereSalePriceEur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereShadingAnalysis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereShadingDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereSolarAnalysisReports($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereSpaceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereTechnicalReports($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereTotalAreaM2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereUsableAreaM2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereVehicleAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereVerifiedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoofMarketplace whereViewsCount($value)
 */
	class RoofMarketplace extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $url
 * @property string $type
 * @property string|null $source_type_description
 * @property string|null $frequency
 * @property \Illuminate\Support\Carbon|null $last_scraped_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\ScrapingSourceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereLastScrapedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereSourceTypeDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingSource whereUrl($value)
 */
	class ScrapingSource extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $platform
 * @property string $handle
 * @property string $url
 * @property int $person_id
 * @property int|null $followers_count
 * @property int $verified
 * @property int $is_public
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Person $person
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereFollowersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialAccount whereVerified($value)
 */
	class SocialAccount extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $comparison_type
 * @property string $period
 * @property string $scope
 * @property int|null $scope_id
 * @property string $user_value
 * @property string $unit
 * @property string|null $average_value
 * @property string|null $median_value
 * @property string|null $best_value
 * @property int|null $user_rank
 * @property int $total_participants
 * @property string|null $percentile
 * @property array<array-key, mixed>|null $breakdown
 * @property array<array-key, mixed>|null $metadata
 * @property bool $is_public
 * @property \Illuminate\Support\Carbon $comparison_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison byPeriod(string $period)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereAverageValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereBestValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereBreakdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereComparisonDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereComparisonType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereMedianValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison wherePercentile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereScopeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereTotalParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereUserRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialComparison whereUserValue($value)
 */
	class SocialComparison extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $interactable_type
 * @property int $interactable_id
 * @property string $interaction_type
 * @property string|null $interaction_note
 * @property array<array-key, mixed>|null $interaction_data
 * @property string|null $source
 * @property string|null $device_type
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property bool $is_public
 * @property bool $notify_author
 * @property bool $show_in_activity
 * @property int $engagement_weight
 * @property numeric $quality_score
 * @property \Illuminate\Support\Carbon|null $interaction_expires_at
 * @property bool $is_temporary
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $interactable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction bookmarks()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction byUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction engagement()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction likes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction loves()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction notExpired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction positive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction recent(int $hours = 24)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction shares()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction shouldNotify()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereEngagementWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereInteractableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereInteractableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereInteractionData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereInteractionExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereInteractionNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereInteractionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereIsTemporary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereNotifyAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereQualityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereShowInActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction withObject(string $type, int $id)
 */
	class SocialInteraction extends \Eloquent {}
}

namespace App\Models{
/**
 * Sistema de contenido patrocinado y publicidad nativa.
 * 
 * Gestiona campañas publicitarias con targeting avanzado,
 * diferentes modelos de pricing y métricas detalladas.
 *
 * @property int $id
 * @property int $sponsor_id
 * @property string $sponsorable_type
 * @property int $sponsorable_id
 * @property string $campaign_name
 * @property string|null $campaign_description
 * @property string $content_type
 * @property array<array-key, mixed>|null $target_audience
 * @property array<array-key, mixed>|null $target_topics
 * @property array<array-key, mixed>|null $target_locations
 * @property array<array-key, mixed>|null $target_demographics
 * @property string $ad_label
 * @property string|null $call_to_action
 * @property string|null $destination_url
 * @property array<array-key, mixed>|null $creative_assets
 * @property string $pricing_model
 * @property numeric $bid_amount
 * @property numeric|null $daily_budget
 * @property numeric|null $total_budget
 * @property numeric $spent_amount
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property array<array-key, mixed>|null $schedule_config
 * @property string $status
 * @property int|null $reviewed_by
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property string|null $review_notes
 * @property int $impressions
 * @property int $clicks
 * @property int $conversions
 * @property numeric $ctr
 * @property numeric $conversion_rate
 * @property numeric $engagement_rate
 * @property bool $show_sponsor_info
 * @property bool $allow_user_feedback
 * @property array<array-key, mixed>|null $disclosure_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $reviewedBy
 * @property-read \App\Models\User $sponsor
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $sponsorable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereAdLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereAllowUserFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereBidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereCallToAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereCampaignDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereCampaignName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereConversionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereConversions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereCreativeAssets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereCtr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereDailyBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereDestinationUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereDisclosureText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereEngagementRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent wherePricingModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereReviewNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereScheduleConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereShowSponsorInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereSpentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereSponsorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereSponsorableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereSponsorableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereTargetAudience($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereTargetDemographics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereTargetLocations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereTargetTopics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereTotalBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SponsoredContent whereUpdatedAt($value)
 */
	class SponsoredContent extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $subject_type
 * @property int $subject_id
 * @property string $key
 * @property string $value
 * @property string $year
 * @property int|null $data_source_id
 * @property string|null $unit
 * @property string|null $confidence_level
 * @property string|null $source_note
 * @property int $is_projection
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DataSource|null $source
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereConfidenceLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereDataSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereIsProjection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereSourceNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereYear($value)
 */
	class Stat extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $type
 * @property string $billing_cycle
 * @property numeric $price
 * @property numeric $setup_fee
 * @property int $trial_days
 * @property int|null $max_projects
 * @property int|null $max_cooperatives
 * @property int|null $max_investments
 * @property int|null $max_consultations
 * @property array<array-key, mixed> $features
 * @property array<array-key, mixed> $limits
 * @property numeric $commission_rate
 * @property bool $priority_support
 * @property bool $verified_badge
 * @property bool $analytics_access
 * @property bool $api_access
 * @property bool $white_label
 * @property bool $is_active
 * @property bool $is_featured
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserSubscription> $activeSubscriptions
 * @property-read int|null $active_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserSubscription> $userSubscriptions
 * @property-read int|null $user_subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan byBillingCycle(string $cycle)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan free()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan orderByPrice(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan paid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereAnalyticsAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereApiAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereBillingCycle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereLimits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereMaxConsultations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereMaxCooperatives($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereMaxInvestments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereMaxProjects($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan wherePrioritySupport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereSetupFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereTrialDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereVerifiedBadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereWhiteLabel($value)
 */
	class SubscriptionPlan extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $data_source_id
 * @property string $status
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property int $processed_items_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DataSource|null $dataSource
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereDataSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereProcessedItemsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SyncLog whereUpdatedAt($value)
 */
	class SyncLog extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $tag_type
 * @property int $is_searchable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Anniversary> $anniversaries
 * @property-read int|null $anniversaries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $people
 * @property-read int|null $people_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PointOfInterest> $pointOfInterests
 * @property-read int|null $point_of_interests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Work> $works
 * @property-read int|null $works_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereIsSearchable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereTagType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagGroup whereUpdatedAt($value)
 */
	class TagGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $offset
 * @property string|null $dst_offset
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Country> $countries
 * @property-read int|null $countries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Municipality> $municipalities
 * @property-read int|null $municipalities_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereDstOffset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereOffset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timezone whereUpdatedAt($value)
 */
	class Timezone extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string|null $icon
 * @property string $color
 * @property string|null $banner_image
 * @property int $creator_id
 * @property array<array-key, mixed>|null $moderator_ids
 * @property string|null $rules
 * @property string $visibility
 * @property string $post_permission
 * @property string $comment_permission
 * @property string $category
 * @property-read int|null $members_count
 * @property-read int|null $posts_count
 * @property-read int|null $comments_count
 * @property numeric $activity_score
 * @property bool $is_featured
 * @property bool $is_active
 * @property bool $requires_approval
 * @property bool $allow_polls
 * @property bool $allow_images
 * @property bool $allow_links
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $activeMembers
 * @property-read int|null $active_members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TopicComment> $comments
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TopicPost> $featuredPosts
 * @property-read int|null $featured_posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $members
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TopicMembership> $memberships
 * @property-read int|null $memberships_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $moderators
 * @property-read int|null $moderators_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TopicPost> $pinnedPosts
 * @property-read int|null $pinned_posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TopicPost> $posts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TopicPost> $publishedPosts
 * @property-read int|null $published_posts_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic accessibleFor(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic byCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic byDifficulty(string $level)
 * @method static \Database\Factories\TopicFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic highActivity(float $minScore = '50')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic popular(int $minMembers = 10)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic recentActivity(int $hours = 24)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic trending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereActivityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereAllowImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereAllowLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereAllowPolls($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereBannerImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereCommentPermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereMembersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereModeratorIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic wherePostPermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic wherePostsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereRequiresApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topic whereVisibility($value)
 */
	class Topic extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $topic_post_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $body
 * @property string|null $excerpt
 * @property int $depth
 * @property string|null $thread_path
 * @property int $sort_order
 * @property-read int|null $children_count
 * @property int $descendants_count
 * @property string $comment_type
 * @property bool $is_best_answer
 * @property int $is_author_reply
 * @property int $is_moderator_reply
 * @property bool $is_pinned
 * @property int $is_highlighted
 * @property int $is_edited
 * @property int $is_deleted
 * @property int $upvotes_count
 * @property int $downvotes_count
 * @property int $score
 * @property int $replies_count
 * @property int $likes_count
 * @property int $reports_count
 * @property int $helpful_votes
 * @property int $not_helpful_votes
 * @property numeric $quality_score
 * @property string $helpfulness_score
 * @property string $relevance_score
 * @property int|null $read_time_seconds
 * @property string $engagement_rate
 * @property array<array-key, mixed>|null $images
 * @property array<array-key, mixed>|null $attachments
 * @property array<array-key, mixed>|null $links
 * @property string|null $code_snippets
 * @property string $status
 * @property string|null $moderation_flags
 * @property string|null $moderation_notes
 * @property int|null $moderated_by
 * @property string|null $moderated_at
 * @property string|null $last_edited_at
 * @property int|null $last_edited_by
 * @property string|null $edit_reason
 * @property int $edit_count
 * @property string|null $edit_history
 * @property array<array-key, mixed>|null $mentioned_users
 * @property array<array-key, mixed>|null $tags
 * @property string $language
 * @property string|null $quote_text
 * @property int|null $quoted_comment_id
 * @property string|null $context_data
 * @property int $notify_parent_author
 * @property int $notify_post_author
 * @property int $notify_followers
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property int $views_count
 * @property int $unique_views_count
 * @property string $ranking_score
 * @property string $controversy_score
 * @property string|null $hot_until
 * @property string|null $source
 * @property string|null $user_agent
 * @property string|null $creation_metadata
 * @property string $author_reputation_at_time
 * @property int|null $root_comment_id
 * @property string|null $thread_participants
 * @property int $breaks_thread
 * @property string|null $thread_last_activity
 * @property int $collapsed_by_default
 * @property int $show_score
 * @property int $allow_replies
 * @property int|null $max_reply_depth
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TopicComment> $children
 * @property-read TopicComment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialInteraction> $socialInteractions
 * @property-read int|null $social_interactions_count
 * @property-read \App\Models\TopicPost $topicPost
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment bestAnswers()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment orderByScore()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment rootComments()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereAllowReplies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereAuthorReputationAtTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereBreaksThread($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereChildrenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereCodeSnippets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereCollapsedByDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereCommentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereContextData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereControversyScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereCreationMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereDepth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereDescendantsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereDownvotesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereEditCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereEditHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereEditReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereEngagementRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereHelpfulVotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereHelpfulnessScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereHotUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereIsAuthorReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereIsBestAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereIsEdited($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereIsHighlighted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereIsModeratorReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereIsPinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereLastEditedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereLastEditedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereMaxReplyDepth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereMentionedUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereModeratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereModeratedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereModerationFlags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereModerationNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereNotHelpfulVotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereNotifyFollowers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereNotifyParentAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereNotifyPostAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereQualityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereQuoteText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereQuotedCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereRankingScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereReadTimeSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereRelevanceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereRepliesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereReportsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereRootCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereShowScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereThreadLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereThreadParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereThreadPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereTopicPostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereUniqueViewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereUpvotesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicComment whereViewsCount($value)
 */
	class TopicComment extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $topic_id
 * @property string $follow_type
 * @property bool $notifications_enabled
 * @property array<array-key, mixed>|null $notification_preferences
 * @property \Illuminate\Support\Carbon $followed_at
 * @property \Illuminate\Support\Carbon|null $last_visited_at
 * @property int $visit_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Topic $topic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing whereFollowType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing whereFollowedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing whereLastVisitedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing whereNotificationPreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing whereNotificationsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicFollowing whereVisitCount($value)
 */
	class TopicFollowing extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $topic_id
 * @property int $user_id
 * @property string $role
 * @property string $status
 * @property bool $notifications_enabled
 * @property bool $email_notifications
 * @property bool $push_notifications
 * @property bool $digest_notifications
 * @property string $notification_frequency
 * @property array<array-key, mixed>|null $notification_preferences
 * @property bool $notify_new_posts
 * @property bool $notify_replies
 * @property bool $notify_mentions
 * @property bool $notify_trending
 * @property bool $notify_announcements
 * @property bool $notify_events
 * @property bool $show_in_main_feed
 * @property bool $prioritize_in_feed
 * @property int $feed_weight
 * @property int $posts_count
 * @property int $comments_count
 * @property int $upvotes_received
 * @property int $downvotes_received
 * @property int $reputation_score
 * @property int $helpful_answers_count
 * @property int $best_answers_count
 * @property int $days_active
 * @property int $consecutive_days_active
 * @property int $posts_this_week
 * @property int $posts_this_month
 * @property numeric $avg_post_score
 * @property numeric $participation_rate
 * @property \Illuminate\Support\Carbon $joined_at
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property \Illuminate\Support\Carbon|null $last_post_at
 * @property \Illuminate\Support\Carbon|null $last_comment_at
 * @property \Illuminate\Support\Carbon|null $last_visit_at
 * @property int $total_visits
 * @property int $total_time_spent_minutes
 * @property array<array-key, mixed>|null $moderation_permissions
 * @property bool $can_pin_posts
 * @property bool $can_feature_posts
 * @property bool $can_delete_posts
 * @property bool $can_ban_users
 * @property bool $can_edit_topic
 * @property string|null $ban_reason
 * @property \Illuminate\Support\Carbon|null $banned_until
 * @property int|null $banned_by
 * @property \Illuminate\Support\Carbon|null $muted_until
 * @property int|null $muted_by
 * @property string|null $moderation_notes
 * @property bool $show_activity_publicly
 * @property bool $allow_direct_messages
 * @property bool $show_online_status
 * @property array<array-key, mixed>|null $topic_badges
 * @property int $featured_posts_count
 * @property int $trending_posts_count
 * @property \Illuminate\Support\Carbon|null $became_contributor_at
 * @property \Illuminate\Support\Carbon|null $became_moderator_at
 * @property array<array-key, mixed>|null $custom_settings
 * @property string|null $custom_title
 * @property string|null $custom_flair
 * @property array<array-key, mixed>|null $interests_in_topic
 * @property int|null $invited_by
 * @property string|null $join_source
 * @property array<array-key, mixed>|null $join_metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $bannedBy
 * @property-read \App\Models\User|null $invitedBy
 * @property-read \App\Models\User|null $mutedBy
 * @property-read \App\Models\Topic $topic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership banned()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership byRole(string $role)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership frequentContributors(int $minPosts = 5)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership highReputation(int $minScore = 100)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership moderators()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership recentlyActive(int $days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereAllowDirectMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereAvgPostScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereBanReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereBannedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereBannedUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereBecameContributorAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereBecameModeratorAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereBestAnswersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereCanBanUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereCanDeletePosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereCanEditTopic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereCanFeaturePosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereCanPinPosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereConsecutiveDaysActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereCustomFlair($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereCustomSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereCustomTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereDaysActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereDigestNotifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereDownvotesReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereEmailNotifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereFeaturedPostsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereFeedWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereHelpfulAnswersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereInterestsInTopic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereInvitedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereJoinMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereJoinSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereLastCommentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereLastPostAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereLastVisitAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereModerationNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereModerationPermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereMutedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereMutedUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereNotificationFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereNotificationPreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereNotificationsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereNotifyAnnouncements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereNotifyEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereNotifyMentions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereNotifyNewPosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereNotifyReplies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereNotifyTrending($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereParticipationRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership wherePostsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership wherePostsThisMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership wherePostsThisWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership wherePrioritizeInFeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership wherePushNotifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereReputationScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereShowActivityPublicly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereShowInMainFeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereShowOnlineStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereTopicBadges($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereTotalTimeSpentMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereTotalVisits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereTrendingPostsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereUpvotesReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicMembership withNotifications()
 */
	class TopicMembership extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $topic_id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property string|null $excerpt
 * @property string|null $summary
 * @property string $post_type
 * @property bool $is_pinned
 * @property bool $is_locked
 * @property bool $is_featured
 * @property bool $is_announcement
 * @property int $is_nsfw
 * @property int $is_spoiler
 * @property int $requires_approval
 * @property bool $allow_comments
 * @property int $notify_replies
 * @property array<array-key, mixed>|null $images
 * @property array<array-key, mixed>|null $videos
 * @property array<array-key, mixed>|null $attachments
 * @property array<array-key, mixed>|null $links
 * @property string|null $thumbnail_url
 * @property int $views_count
 * @property int $unique_views_count
 * @property int $upvotes_count
 * @property int $downvotes_count
 * @property int $score
 * @property-read int|null $comments_count
 * @property int $shares_count
 * @property int $bookmarks_count
 * @property int $likes_count
 * @property int $reports_count
 * @property numeric $quality_score
 * @property string $helpfulness_score
 * @property string $engagement_rate
 * @property int|null $read_time_seconds
 * @property string $completion_rate
 * @property numeric $trending_score
 * @property numeric $hot_score
 * @property string $relevance_score
 * @property int $controversy_score
 * @property string|null $trending_until
 * @property string $status
 * @property string|null $moderation_flags
 * @property string|null $moderation_notes
 * @property int|null $moderated_by
 * @property string|null $moderated_at
 * @property string|null $rejection_reason
 * @property string|null $poll_options
 * @property string|null $poll_expires_at
 * @property int $poll_multiple_choice
 * @property string|null $event_details
 * @property string|null $event_start_at
 * @property string|null $event_end_at
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property string|null $location_name
 * @property string|null $location_region
 * @property array<array-key, mixed>|null $tags
 * @property string|null $mentioned_users
 * @property string|null $related_posts
 * @property string $language
 * @property int $is_edited
 * @property string|null $last_edited_at
 * @property int|null $last_edited_by
 * @property string|null $edit_reason
 * @property int $edit_count
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property string|null $last_comment_at
 * @property int|null $last_comment_by
 * @property string|null $bumped_at
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $structured_data
 * @property string|null $algorithm_data
 * @property string $author_reputation_at_time
 * @property string|null $source
 * @property string|null $user_agent
 * @property string|null $creation_metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TopicComment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TopicComment> $publishedComments
 * @property-read int|null $published_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialInteraction> $socialInteractions
 * @property-read int|null $social_interactions_count
 * @property-read \App\Models\Topic $topic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost hot()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost trending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereAlgorithmData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereAllowComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereAuthorReputationAtTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereBookmarksCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereBumpedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereCompletionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereControversyScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereCreationMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereDownvotesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereEditCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereEditReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereEngagementRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereEventDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereEventEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereEventStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereHelpfulnessScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereHotScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereIsAnnouncement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereIsEdited($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereIsLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereIsNsfw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereIsPinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereIsSpoiler($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereLastCommentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereLastCommentBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereLastEditedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereLastEditedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereLocationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereLocationRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereMentionedUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereModeratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereModeratedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereModerationFlags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereModerationNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereNotifyReplies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost wherePollExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost wherePollMultipleChoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost wherePollOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost wherePostType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereQualityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereReadTimeSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereRelatedPosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereRelevanceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereReportsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereRequiresApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereSharesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereStructuredData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereThumbnailUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereTrendingScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereTrendingUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereUniqueViewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereUpvotesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereVideos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopicPost whereViewsCount($value)
 */
	class TopicPost extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Achievement> $achievements
 * @property-read int|null $achievements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Challenge> $activeChallenges
 * @property-read int|null $active_challenges_count
 * @property-read \App\Models\UserSubscription|null $activeSubscription
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ActivityFeed> $activityFeeds
 * @property-read int|null $activity_feeds_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserBadge> $badges
 * @property-read int|null $badges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialInteraction> $bookmarksGiven
 * @property-read int|null $bookmarks_given_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Challenge> $challenges
 * @property-read int|null $challenges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectCommission> $commissions
 * @property-read int|null $commissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Achievement> $completedAchievements
 * @property-read int|null $completed_achievements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ConsultationService> $consultationsAsClient
 * @property-read int|null $consultations_as_client_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ConsultationService> $consultationsAsConsultant
 * @property-read int|null $consultations_as_consultant_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CooperativeUserMember> $cooperativeMemberships
 * @property-read int|null $cooperative_memberships_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CooperativePost> $cooperativePosts
 * @property-read int|null $cooperative_posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cooperative> $cooperatives
 * @property-read int|null $cooperatives_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExpertVerification> $expertVerifications
 * @property-read int|null $expert_verifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ActivityFeed> $featuredActivities
 * @property-read int|null $featured_activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserFollow> $followerRelationships
 * @property-read int|null $follower_relationships_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $followers
 * @property-read int|null $followers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $following
 * @property-read int|null $following_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserFollow> $followingRelationships
 * @property-read int|null $following_relationships_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialInteraction> $likesGiven
 * @property-read int|null $likes_given_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ActivityFeed> $milestones
 * @property-read int|null $milestones_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NotificationSetting> $notificationSettings
 * @property-read int|null $notification_settings_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserPrivilege> $privileges
 * @property-read int|null $privileges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectVerification> $projectVerifications
 * @property-read int|null $project_verifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ActivityFeed> $publicActivities
 * @property-read int|null $public_activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialInteraction> $sharesGiven
 * @property-read int|null $shares_given_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialComparison> $socialComparisons
 * @property-read int|null $social_comparisons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialInteraction> $socialInteractions
 * @property-read int|null $social_interactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Stat> $stats
 * @property-read int|null $stats_count
 * @property-read \App\Models\SubscriptionPlan|null $subscriptionPlan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserSubscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TopicFollowing> $topicFollowings
 * @property-read int|null $topic_followings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserAchievement> $userAchievements
 * @property-read int|null $user_achievements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserChallenge> $userChallenges
 * @property-read int|null $user_challenges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserDevice> $userDevices
 * @property-read int|null $user_devices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectVerification> $verificationsDone
 * @property-read int|null $verifications_done_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * Modelo UserAchievement para la relación usuario-logros.
 * 
 * Gestiona el progreso y estado de los logros para cada usuario,
 * incluyendo progreso parcial, niveles y metadatos.
 *
 * @property int $id
 * @property int $user_id
 * @property int $achievement_id
 * @property int $progress
 * @property int $level
 * @property bool $is_completed
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property array<array-key, mixed>|null $metadata
 * @property numeric|null $value_achieved
 * @property int $points_earned
 * @property bool $is_notified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Achievement $achievement
 * @property-read float $progress_percentage
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement inProgress()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement notNotified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereAchievementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereIsNotified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement wherePointsEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereValueAchieved($value)
 */
	class UserAchievement extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $badge_type
 * @property string $category
 * @property string $name
 * @property string $description
 * @property string|null $icon_url
 * @property string $color
 * @property array<array-key, mixed> $criteria
 * @property array<array-key, mixed>|null $metadata
 * @property int $points_awarded
 * @property bool $is_public
 * @property bool $is_featured
 * @property \Illuminate\Support\Carbon $earned_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge byCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge valid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereBadgeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereCriteria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereEarnedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge wherePointsAwarded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereUserId($value)
 */
	class UserBadge extends \Eloquent {}
}

namespace App\Models{
/**
 * Sistema de bookmarks/favoritos personalizado estilo Pocket.
 * 
 * Permite a los usuarios guardar cualquier tipo de contenido
 * con organización avanzada, notas y recordatorios.
 *
 * @property int $id
 * @property int $user_id
 * @property string $bookmarkable_type
 * @property int $bookmarkable_id
 * @property string|null $folder
 * @property array<array-key, mixed>|null $tags
 * @property string|null $personal_notes
 * @property int $priority
 * @property bool $reminder_enabled
 * @property \Illuminate\Support\Carbon|null $reminder_date
 * @property string|null $reminder_frequency
 * @property int $access_count
 * @property \Illuminate\Support\Carbon|null $last_accessed_at
 * @property bool $is_public
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $bookmarkable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereAccessCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereBookmarkableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereBookmarkableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereFolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereLastAccessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark wherePersonalNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereReminderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereReminderEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereReminderFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBookmark whereUserId($value)
 */
	class UserBookmark extends \Eloquent {}
}

namespace App\Models{
/**
 * Modelo UserChallenge para la participación en retos.
 * 
 * Gestiona el estado y progreso de los usuarios en challenges,
 * incluyendo ranking, puntos y recompensas obtenidas.
 *
 * @property int $id
 * @property int $user_id
 * @property int $challenge_id
 * @property string $status
 * @property \Illuminate\Support\Carbon $joined_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property array<array-key, mixed>|null $progress
 * @property numeric $current_value
 * @property int|null $ranking_position
 * @property int $points_earned
 * @property numeric $reward_earned
 * @property array<array-key, mixed>|null $achievements_unlocked
 * @property string|null $notes
 * @property bool $is_team_leader
 * @property int|null $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Challenge $challenge
 * @property-read float $progress_percentage
 * @property-read string $status_name
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge byStatus(string $status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge ranked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereAchievementsUnlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereChallengeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereCurrentValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereIsTeamLeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge wherePointsEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereRankingPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereRewardEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserChallenge whereUserId($value)
 */
	class UserChallenge extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $device_name
 * @property string|null $device_type
 * @property string|null $platform
 * @property string|null $browser
 * @property string|null $ip_address
 * @property string|null $token
 * @property int $notifications_enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereDeviceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereNotificationsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereUserId($value)
 */
	class UserDevice extends \Eloquent {}
}

namespace App\Models{
/**
 * Sistema de endorsements/validaciones de habilidades estilo LinkedIn.
 * 
 * Permite a los usuarios validar las habilidades y conocimientos
 * de otros usuarios con contexto y métricas de confianza.
 *
 * @property int $id
 * @property int $endorser_id
 * @property int $endorsed_id
 * @property string $skill_category
 * @property string|null $specific_skill
 * @property string|null $endorsement_text
 * @property numeric|null $skill_rating
 * @property string $relationship_context
 * @property string|null $project_context
 * @property int|null $collaboration_duration_months
 * @property bool $is_verified
 * @property numeric $trust_score
 * @property int $helpful_votes
 * @property int $total_votes
 * @property bool $is_public
 * @property bool $show_on_profile
 * @property bool $notify_endorsed
 * @property bool $is_mutual
 * @property int|null $reciprocal_endorsement_id
 * @property string $status
 * @property int|null $disputed_by
 * @property string|null $dispute_reason
 * @property \Illuminate\Support\Carbon|null $disputed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $disputedBy
 * @property-read \App\Models\User $endorsed
 * @property-read \App\Models\User $endorser
 * @property-read UserEndorsement|null $reciprocal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereCollaborationDurationMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereDisputeReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereDisputedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereDisputedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereEndorsedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereEndorsementText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereEndorserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereHelpfulVotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereIsMutual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereNotifyEndorsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereProjectContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereReciprocalEndorsementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereRelationshipContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereShowOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereSkillCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereSkillRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereSpecificSkill($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereTotalVotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereTrustScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEndorsement whereUpdatedAt($value)
 */
	class UserEndorsement extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $follower_id
 * @property int $following_id
 * @property string $follow_type
 * @property bool $notify_new_activity
 * @property bool $notify_achievements
 * @property bool $notify_projects
 * @property bool $notify_investments
 * @property bool $notify_milestones
 * @property bool $notify_content
 * @property string $notification_frequency
 * @property bool $show_in_main_feed
 * @property bool $prioritize_in_feed
 * @property int $feed_weight
 * @property string|null $follow_reason
 * @property array<array-key, mixed>|null $interests
 * @property array<array-key, mixed>|null $tags
 * @property bool $is_mutual
 * @property \Illuminate\Support\Carbon|null $mutual_since
 * @property int $interactions_count
 * @property \Illuminate\Support\Carbon|null $last_interaction_at
 * @property numeric $engagement_score
 * @property int $content_views
 * @property bool $is_public
 * @property bool $show_to_followed
 * @property bool $allow_followed_to_see_activity
 * @property array<array-key, mixed>|null $content_filters
 * @property array<array-key, mixed>|null $activity_filters
 * @property numeric $minimum_relevance_score
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $status_changed_at
 * @property string|null $status_reason
 * @property \Illuminate\Support\Carbon $followed_at
 * @property \Illuminate\Support\Carbon|null $last_seen_activity_at
 * @property int $days_following
 * @property numeric $relevance_decay_rate
 * @property array<array-key, mixed>|null $algorithm_preferences
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $follower
 * @property-read \App\Models\User $following
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow byFollower(\App\Models\User $follower)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow byFollowing(\App\Models\User $following)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow byNotificationFrequency(string $frequency)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow dailyDigest()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow highEngagement(float $minScore = '50')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow inMainFeed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow instantNotification()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow longTerm(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow mutual()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow prioritized()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereActivityFilters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereAlgorithmPreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereAllowFollowedToSeeActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereContentFilters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereContentViews($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereDaysFollowing($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereEngagementScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereFeedWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereFollowReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereFollowType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereFollowedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereFollowerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereFollowingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereInteractionsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereInterests($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereIsMutual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereLastInteractionAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereLastSeenActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereMinimumRelevanceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereMutualSince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereNotificationFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereNotifyAchievements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereNotifyContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereNotifyInvestments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereNotifyMilestones($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereNotifyNewActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereNotifyProjects($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow wherePrioritizeInFeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereRelevanceDecayRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereShowInMainFeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereShowToFollowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereStatusReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFollow withNotifications()
 */
	class UserFollow extends \Eloquent {}
}

namespace App\Models{
/**
 * Contenido generado por usuarios para engagement y participación.
 * 
 * Sistema completo de gestión de contenido generado por usuarios
 * incluyendo comentarios, reseñas, reportes, preguntas, respuestas
 * y otros tipos de participación con moderación avanzada.
 *
 * @property int $id
 * @property int|null $user_id Usuario que creó el contenido
 * @property string $related_type Tipo del modelo relacionado
 * @property int $related_id ID del modelo relacionado
 * @property string $content_type Tipo de contenido
 * @property string $content Contenido principal
 * @property string|null $title Título del contenido
 * @property string|null $excerpt Extracto del contenido
 * @property string $language Idioma del contenido
 * @property string $visibility Visibilidad del contenido
 * @property string $status Estado del contenido
 * @property int|null $parent_id Contenido padre (para respuestas)
 * @property float|null $rating Calificación (1-5)
 * @property array|null $metadata Metadatos adicionales
 * @property array|null $media_attachments Archivos adjuntos
 * @property string|null $user_name Nombre del usuario (si anónimo)
 * @property string|null $user_email Email del usuario (si anónimo)
 * @property string|null $user_ip IP del usuario
 * @property string|null $user_agent User agent del navegador
 * @property bool $is_anonymous Si es contenido anónimo
 * @property bool $is_verified Si está verificado
 * @property bool $is_featured Si está destacado
 * @property bool $is_spam Si es marcado como spam
 * @property bool $needs_moderation Si necesita moderación
 * @property int $likes_count Número de likes
 * @property int $dislikes_count Número de dislikes
 * @property int $replies_count Número de respuestas
 * @property int $reports_count Número de reportes
 * @property float|null $sentiment_score Puntuación de sentimiento
 * @property string|null $sentiment_label Etiqueta de sentimiento
 * @property array|null $moderation_notes Notas de moderación
 * @property array|null $auto_tags Tags automáticos
 * @property int|null $moderator_id Moderador que revisó
 * @property string|null $location_name Ubicación mencionada
 * @property float|null $latitude Latitud si tiene ubicación
 * @property float|null $longitude Longitud si tiene ubicación
 * @property \Carbon\Carbon|null $published_at Fecha de publicación
 * @property \Carbon\Carbon|null $moderated_at Fecha de moderación
 * @property \Carbon\Carbon|null $featured_until Destacado hasta
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Model $related
 * @property-read \App\Models\UserGeneratedContent|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserGeneratedContent[] $replies
 * @property-read \App\Models\User|null $moderator
 * @property string $type
 * @property-read mixed $author_info
 * @property-read mixed $content_status
 * @property-read mixed $content_type_name
 * @property-read mixed $engagement_metrics
 * @property-read mixed $related_url
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent comments()
 * @method static \Database\Factories\UserGeneratedContentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent highRated($minRating = '4')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent needsModeration()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent notSpam()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent popular($minLikes = 10)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent questions()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent recent($days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent reports()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent reviews()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent search($search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent topLevel()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent verified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereRelatedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserGeneratedContent whereUserId($value)
 */
	class UserGeneratedContent extends \Eloquent {}
}

namespace App\Models{
/**
 * Sistema de listas personalizadas estilo Twitter/Discord.
 * 
 * Permite a los usuarios crear listas curadas de contenido
 * con funcionalidades de colaboración y auto-curación.
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon
 * @property string $color
 * @property string|null $cover_image
 * @property string $list_type
 * @property array<array-key, mixed>|null $allowed_content_types
 * @property string $visibility
 * @property array<array-key, mixed>|null $collaborator_ids
 * @property bool $allow_suggestions
 * @property bool $allow_comments
 * @property string $curation_mode
 * @property array<array-key, mixed>|null $auto_criteria
 * @property-read int|null $items_count
 * @property int $followers_count
 * @property int $views_count
 * @property int $shares_count
 * @property numeric $engagement_score
 * @property bool $is_featured
 * @property bool $is_template
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ListItem> $activeItems
 * @property-read int|null $active_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ListItem> $items
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\UserListFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereAllowComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereAllowSuggestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereAllowedContentTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereAutoCriteria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereCollaboratorIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereCurationMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereEngagementScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereFollowersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereIsTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereItemsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereListType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereSharesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereViewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserList whereVisibility($value)
 */
	class UserList extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $privilege_type
 * @property string $scope
 * @property int|null $scope_id
 * @property int $level
 * @property bool $is_active
 * @property array<array-key, mixed>|null $permissions
 * @property array<array-key, mixed>|null $limits
 * @property int $reputation_required
 * @property \Illuminate\Support\Carbon $granted_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property int|null $granted_by
 * @property string|null $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $grantor
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege byScope(string $scope, $scopeId = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege minLevel(int $level)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege valid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereGrantedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereGrantedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereLimits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege wherePrivilegeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereReputationRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereScopeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPrivilege whereUserId($value)
 */
	class UserPrivilege extends \Eloquent {}
}

namespace App\Models{
/**
 * Sistema de reputación de usuario estilo StackOverflow.
 * 
 * Gestiona la reputación global y por categorías de cada usuario,
 * incluyendo métricas de contribución, engagement y liderazgo.
 *
 * @property int $id
 * @property int $user_id Usuario
 * @property int $total_reputation Reputación total
 * @property array|null $category_reputation Reputación por categoría
 * @property array|null $topic_reputation Reputación por tema
 * @property int $helpful_answers Respuestas útiles
 * @property int $accepted_solutions Soluciones aceptadas
 * @property int $quality_posts Posts de calidad
 * @property int $verified_contributions Contribuciones verificadas
 * @property int $upvotes_received Upvotes recibidos
 * @property int $downvotes_received Downvotes recibidos
 * @property float $upvote_ratio Ratio de upvotes
 * @property int $topics_created Temas creados
 * @property int $successful_projects Proyectos exitosos
 * @property int $mentorship_points Puntos de mentoría
 * @property int $warnings_received Advertencias recibidas
 * @property int $content_removed Contenido eliminado
 * @property bool $is_suspended Si está suspendido
 * @property \Carbon\Carbon|null $suspended_until Hasta cuándo suspendido
 * @property int|null $global_rank Ranking global
 * @property array|null $category_ranks Rankings por categoría
 * @property int|null $monthly_rank Ranking mensual
 * @property bool $is_verified_professional Si es profesional verificado
 * @property array|null $professional_credentials Credenciales profesionales
 * @property array|null $expertise_areas Áreas de expertise
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user Usuario
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ReputationTransaction[] $transactions Transacciones
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereAcceptedSolutions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereCategoryRanks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereCategoryReputation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereContentRemoved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereDownvotesReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereExpertiseAreas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereGlobalRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereHelpfulAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereIsSuspended($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereIsVerifiedProfessional($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereMentorshipPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereMonthlyRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereProfessionalCredentials($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereQualityPosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereSuccessfulProjects($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereSuspendedUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereTopicReputation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereTopicsCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereTotalReputation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereUpvoteRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereUpvotesReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereVerifiedContributions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReputation whereWarningsReceived($value)
 */
	class UserReputation extends \Eloquent {}
}

namespace App\Models{
/**
 * Sistema de reviews y ratings estilo Google/Quora.
 * 
 * Permite a los usuarios hacer reviews detalladas de servicios,
 * productos y experiencias con verificación y respuestas del proveedor.
 *
 * @property int $id
 * @property int $reviewer_id
 * @property string $reviewable_type
 * @property int $reviewable_id
 * @property numeric $overall_rating
 * @property array<array-key, mixed>|null $detailed_ratings
 * @property string $title
 * @property string $content
 * @property array<array-key, mixed>|null $pros
 * @property array<array-key, mixed>|null $cons
 * @property array<array-key, mixed>|null $images
 * @property array<array-key, mixed>|null $attachments
 * @property string $service_type
 * @property \Illuminate\Support\Carbon|null $service_date
 * @property numeric|null $service_cost
 * @property string|null $service_location
 * @property int|null $service_duration_days
 * @property bool $is_verified_purchase
 * @property string|null $verification_code
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property int|null $verified_by
 * @property bool|null $would_recommend
 * @property string|null $recommendation_level
 * @property int $helpful_votes
 * @property int $not_helpful_votes
 * @property int $total_votes
 * @property numeric $helpfulness_ratio
 * @property int $views_count
 * @property string|null $provider_response
 * @property \Illuminate\Support\Carbon|null $provider_responded_at
 * @property int|null $provider_responder_id
 * @property string $status
 * @property int $flags_count
 * @property array<array-key, mixed>|null $flag_reasons
 * @property int|null $moderated_by
 * @property \Illuminate\Support\Carbon|null $moderated_at
 * @property string|null $moderation_notes
 * @property bool $is_anonymous
 * @property bool $show_service_cost
 * @property bool $allow_contact
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $moderatedBy
 * @property-read \App\Models\User|null $providerResponder
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $reviewable
 * @property-read \App\Models\User $reviewer
 * @property-read \App\Models\User|null $verifiedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereAllowContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereCons($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereDetailedRatings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereFlagReasons($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereFlagsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereHelpfulVotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereHelpfulnessRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereIsAnonymous($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereIsVerifiedPurchase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereModeratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereModeratedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereModerationNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereNotHelpfulVotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereOverallRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview wherePros($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereProviderRespondedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereProviderResponderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereProviderResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereRecommendationLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereReviewableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereReviewableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereReviewerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereServiceCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereServiceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereServiceDurationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereServiceLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereServiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereShowServiceCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereTotalVotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereVerificationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereVerifiedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereViewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserReview whereWouldRecommend($value)
 */
	class UserReview extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $subscription_plan_id
 * @property string $status
 * @property numeric $amount_paid
 * @property string $currency
 * @property string $billing_cycle
 * @property \Illuminate\Support\Carbon $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon|null $trial_ends_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon|null $next_billing_at
 * @property string|null $payment_method
 * @property string|null $external_subscription_id
 * @property array<array-key, mixed>|null $usage_stats
 * @property array<array-key, mixed>|null $metadata
 * @property string|null $cancellation_reason
 * @property bool $auto_renew
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\SubscriptionPlan $subscriptionPlan
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription cancelled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription trial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription upcomingBilling(int $days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereAutoRenew($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereBillingCycle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereCancellationReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereExternalSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereNextBillingAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereSubscriptionPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereUsageStats($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubscription whereUserId($value)
 */
	class UserSubscription extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $address
 * @property int $municipality_id
 * @property string|null $latitude
 * @property string|null $longitude
 * @property int|null $capacity
 * @property string|null $description
 * @property string|null $venue_type
 * @property string $venue_status
 * @property int $is_verified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \App\Models\Municipality $municipality
 * @property-read \App\Models\VenueType|null $venueType
 * @method static \Database\Factories\VenueFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereVenueStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereVenueType($value)
 */
	class Venue extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Venue> $venues
 * @property-read int|null $venues_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueType whereUpdatedAt($value)
 */
	class VenueType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Color> $colors
 * @property-read int|null $colors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Font> $fonts
 * @property-read int|null $fonts_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VisualIdentity whereUpdatedAt($value)
 */
	class VisualIdentity extends \Eloquent {}
}

namespace App\Models{
/**
 * Datos meteorológicos y solares para optimización energética.
 * 
 * Almacena información meteorológica histórica y de predicción
 * junto con datos de irradiación solar para optimizar la
 * producción de instalaciones fotovoltaicas y eólicas.
 *
 * @property int $id
 * @property \Carbon\Carbon $datetime Fecha y hora del registro
 * @property string|null $location Ubicación geográfica
 * @property int|null $municipality_id Municipio
 * @property float|null $latitude Latitud
 * @property float|null $longitude Longitud
 * @property float|null $temperature Temperatura en °C
 * @property float|null $temperature_min Temperatura mínima °C
 * @property float|null $temperature_max Temperatura máxima °C
 * @property float|null $humidity Humedad relativa %
 * @property float|null $cloud_coverage Nubosidad %
 * @property float|null $solar_irradiance Irradiación solar W/m²
 * @property float|null $solar_irradiance_daily Irradiación diaria kWh/m²
 * @property float|null $uv_index Índice UV
 * @property float|null $wind_speed Velocidad viento m/s
 * @property float|null $wind_direction Dirección viento grados
 * @property float|null $wind_gust Ráfagas viento m/s
 * @property float|null $precipitation Precipitación mm
 * @property float|null $pressure Presión atmosférica hPa
 * @property float|null $visibility Visibilidad km
 * @property string|null $weather_condition Condición meteorológica
 * @property string $data_type Tipo: historical, current, forecast
 * @property string $source Fuente de datos
 * @property string|null $source_url URL de la fuente
 * @property float|null $solar_potential Potencial solar estimado kWh/kWp
 * @property float|null $wind_potential Potencial eólico estimado
 * @property bool $is_optimal_solar Si es óptimo para solar
 * @property bool $is_optimal_wind Si es óptimo para eólico
 * @property int|null $air_quality_index Índice calidad aire
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SolarProduction[] $solarProductions
 * @property-read mixed $conditions_summary
 * @property-read mixed $optimization_recommendations
 * @property-read mixed $solar_quality_class
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData betweenDates($start, $end)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData current()
 * @method static \Database\Factories\WeatherAndSolarDataFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData forecast()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData historical()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData nearLocation($lat, $lng, $radiusKm = 50)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData optimalSolar()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData optimalWind()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereAirQualityIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereCloudCoverage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereDataType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereHumidity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereIsOptimalSolar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereIsOptimalWind($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData wherePrecipitation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData wherePressure($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereSolarIrradiance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereSolarIrradianceDaily($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereSolarPotential($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereSourceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereTemperatureMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereTemperatureMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereUvIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereWeatherCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereWindDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereWindGust($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereWindPotential($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherAndSolarData whereWindSpeed($value)
 */
	class WeatherAndSolarData extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Work
 * 
 * Represents a work (book, film, etc.) associated with a person.
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $type
 * @property string|null $description
 * @property int|null $release_year
 * @property int|null $person_id
 * @property string|null $genre
 * @property int|null $language_id
 * @property int|null $link_id
 * @property-read Person $person
 * @property-read Language $language
 * @property-read Link $link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $cast
 * @property-read int|null $cast_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $directors
 * @property-read int|null $directors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $leads
 * @property-read int|null $leads_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Link> $links
 * @property-read int|null $links_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $people
 * @property-read int|null $people_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $writers
 * @property-read int|null $writers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereGenre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereReleaseYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Work whereUpdatedAt($value)
 */
	class Work extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $climate_zone
 * @property string|null $description
 * @property string|null $average_heating_demand
 * @property string|null $average_cooling_demand
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereAverageCoolingDemand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereAverageHeatingDemand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereClimateZone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ZoneClimate whereUpdatedAt($value)
 */
	class ZoneClimate extends \Eloquent {}
}

