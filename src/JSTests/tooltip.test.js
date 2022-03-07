/**
 * @jest-environment jsdom
 */
import Tooltip from '../vue-components/tooltip'

describe('tooltip',()=>{
    it('tooltip',()=>{
        expect(Tooltip.test.type).toBe(String);
    });
});