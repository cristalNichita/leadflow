export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-8 items-center justify-center overflow-hidden rounded-lg">
                <img
                    src="/leadflow-logo.png"
                    alt="LeadFlow"
                    className="size-8 object-cover"
                />
            </div>

            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="truncate leading-tight font-semibold">
                    LeadFlow
                </span>
            </div>
        </>
    );
}
