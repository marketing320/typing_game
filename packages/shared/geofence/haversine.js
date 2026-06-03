const EARTH_RADIUS_METERS = 6371000;

/**
 * Calculate distance between two lat/lng points in meters (Haversine formula).
 */
export function haversineDistance(lat1, lon1, lat2, lon2) {
    const toRad = (deg) => (deg * Math.PI) / 180;

    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);

    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) ** 2;

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return EARTH_RADIUS_METERS * c;
}

/**
 * Check if a point is within a radius of a center point.
 */
export function isWithinRadius(userLat, userLon, centerLat, centerLon, radiusMeters) {
    const distance = haversineDistance(userLat, userLon, centerLat, centerLon);
    return { within: distance <= radiusMeters, distance };
}
