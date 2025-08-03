import { NextRequest, NextResponse } from 'next/server';

export async function GET(request: NextRequest, { params }: { params: Promise<{ id: string }> }) {
    const { id } = await params;
    const searchParams = request.nextUrl.searchParams;
    let apiUrl = '';

    switch (id) {
        case 'pull':
            apiUrl = 'https://github.raihanmiraj.com/api/pull.php';
            break;
        case 'push':
            apiUrl = 'https://github.raihanmiraj.com/api/push.php';
            break;
        case 'branch-list':
            apiUrl = 'https://github.raihanmiraj.com/api/branch-list.php';
            break;
        case 'create-branch':
            const createBranchName = searchParams.get('name');
            if (!createBranchName) {
                return NextResponse.json({ error: 'Branch name is required' }, { status: 400 });
            }
            apiUrl = `https://github.raihanmiraj.com/api/branch-create.php?name=${encodeURIComponent(createBranchName)}`;
            break;
        case 'switch-branch':
            const switchBranchName = searchParams.get('name');
            if (!switchBranchName) {
                return NextResponse.json({ error: 'Branch name is required' }, { status: 400 });
            }
            apiUrl = `https://github.raihanmiraj.com/api/branch-switch.php?name=${encodeURIComponent(switchBranchName)}`;
            break;
        case 'delete-branch':
            const branchName = searchParams.get('name');
            if (!branchName) {
                return NextResponse.json({ error: 'Branch name is required' }, { status: 400 });
            }
            apiUrl = `https://github.raihanmiraj.com/api/branch-delete.php?name=${encodeURIComponent(branchName)}`;
            break;
        default:
            return NextResponse.json({ error: 'Invalid action' }, { status: 400 });
    }

    try {
        const response = await fetch(apiUrl);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        return NextResponse.json(data);
    } catch (error) {
        console.error('Error fetching data:', error);
        return NextResponse.json({ error: 'Failed to fetch data' }, { status: 500 });
    }
}