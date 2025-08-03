import { NextRequest, NextResponse } from 'next/server';
import axios from 'axios';

export async function GET(request) {
    try {
        const response = await axios.get('https://github.raihanmiraj.com/api/pull.php');
        const data = response.data;
        return NextResponse.json(data);
    } catch (error) {
        console.error('Error fetching data:', error);
        return NextResponse.json({ error: 'Failed to fetch data' }, { status: 500 });
    }
}