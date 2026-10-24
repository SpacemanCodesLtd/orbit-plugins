export function currentDependencyCheck(root, check, folder) {
    return !!check && !!root.snapshot?.fingerprint && check.fingerprint === root.snapshot.fingerprint
        && ['Current', 'Partial', 'Queued', 'Scanning'].includes(root.scan_state) && !root.scan_error
        && (!folder?.availability || folder.availability === 'Available');
}

export function dependencyName(ecosystem, name) {
    if (ecosystem === 'python') { return name.toLowerCase().replace(/[-_.]+/g, '-'); }
    return ecosystem === 'nuget' ? name.toLowerCase() : name;
}
